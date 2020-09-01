<?php

namespace kajimachi\pages;

use kajimachi\common\DB;

class InitializeController
{
    public function createDatabase()
    {
        if(!DEBUG_MODE)
            exit;
        
        try
        {
            $ptr = DB::open();
            $ptr->query('CREATE TABLE IF NOT EXISTS article(id integer primary key, title text not null, body text not null, visible boolean, posted_at text, updated_at text, deleted_at text);');
            $ptr->query('CREATE TABLE IF NOT EXISTS tag(id integer primary key, name text unique not null);');
            $ptr->query('CREATE TABLE IF NOT EXISTS article_tags(id integer primary key, article_id integer, tag_id integer, foreign key(article_id) references article(id), foreign key(tag_id) references tag(id));');
        }
        catch(PDOException $e)
        {

        }
    }
}
