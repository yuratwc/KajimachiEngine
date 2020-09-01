<?php
namespace kajimachi\pages;

use kajimachi\common\DB;
use kajimachi\common\Auth;
use kajimachi\common\Transaction;
use kajimachi\common\Util;

class ArticleController
{
    public function articles()
    {
        $page = 0;
        $keyword = '';

        if(isset($_GET['p']))
            $page = intval($_GET['p']);
        if(isset($_GET['w']))
            $keyword = $_GET['w'];

        $keyword = '%' . $keyword . '%';
        $q = DB::fetchPrepare('select distinct id, title, posted_at, updated_at from article where visible == 1 and deleted_at is NULL and (title like ? or body like ?) order by posted_at desc limit ? offset ? ;', $keyword, $keyword, PAGE_MAX, PAGE_MAX * $page);

        Util::headJson();
        echo json_encode($q);
    }

    public function remove()
    {
        if(!Auth::check())   
            return;
        
        $raw_json = file_get_contents("php://input");
        $json = json_decode($raw_json, true);
        if(!isset($json['id']))
            return;
        
        $id = intval($json['id']);
        $now = date('Y-m-d H:i:s');
        $db = DB::begin();
        $db->do('update article set deleted_at = ? where id = ?', $now, $id);
        $db->close();
    }

    public function post()
    {
        if(!Auth::check())   
            return;
        
        $raw_json = file_get_contents("php://input");

        $json = json_decode($raw_json, true);

        if(!isset($json['title']) || !isset($json['body']))
            return;

        if(!isset($json['posted_at']))
            $json['posted_at'] = date('Y-m-d H:i:s');

        if(!isset($json['updated_at']))
            $json['updated_at'] = $json['posted_at'];
    
        if(!isset($json['visible']))
            $json['visible'] = 1;

        $db = DB::begin();
        $db->do('INSERT INTO article(title, body, visible, posted_at, updated_at) values(?, ?, ?, ?, ?);', $json['title'], $json['body'], $json['visible'], $json['posted_at'], $json['updated_at']);

        if(isset($json['tags']))
        {
            $article_map = $db->get('select id from article orderby id desc limit 1;');
            $article_id = intval($article_map[0]['id']);
            $tags = [];
            $tag_count = 0;
            foreach($json['tags'] as $tag)
            {
                if(strpos($tag, ' ') === false)
                {
                    $db->do('INSERT INTO tags(name) values(?);', $tag);
                    $tags[] = $tag;
                    $tag_count++;
                }
            }
            $arg = str_repeat(',?', $tag_count);
            $arg = substr($arg, 1);
            $tag_map = $db->get('select id from tag where name in (' . $arg . ');', ...$tags);
            foreach($tag_map as $value)
            {
                $db->do('insert into article_tags(article_id, tag_id) values(?, ?);', $article_id, intval($value['id']));
            }

        }
        $db->close();

    }

    public function show($ary)
    {
        if(!isset($ary['id']) || $ary['id'] === 'post' || $ary['id'] === 'remove')
            return;
        
        $id = intval($ary['id']);

        $t = DB::fetchPrepare('select * from article where id = ?;', $id);

        Util::headJson();
        if(count($t) == 0 || (isset($t[0]['deleted_at']) && $t[0]['deleted_at'] != null))
        {
            echo json_encode(array('status'=>'-1'));
        }
        else if(isset($t[0]['visible']) && $t[0]['visible'] === '0')
        {
            echo json_encode(array('status'=>'0'));
        }
        else
        {
            echo json_encode(array(
                'status' => 1,
                'title' => $t[0]['title'],
                'raw_body' => $t[0]['body'],
                'posted_at' => $t[0]['posted_at'],
                'updated_at' => $t[0]['updated_at']
            ));
        }
    }
}
