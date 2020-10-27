<?php

namespace modules\reviewermodule\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class ReviewerSubmissionsQuery extends ElementQuery
{
    public $first_name;
    public $last_name;
    public $email;

    public function first_name($value)
    {
        $this->first_name = $value;

        return $this;
    }

    public function last_name($value)
    {
        $this->last_name = $value;

        return $this;
    }

    public function email($value)
    {
        $this->email = $value;

        return $this;
    }


    protected function beforePrepare(): bool
    {
//        // join in the products table
        $this->joinElementTable('reviewermodule_person');

        // select the columns
        $this->query->select([
            'reviewermodule_person.first_name',
            'reviewermodule_person.last_name',
            'reviewermodule_person.email',

        ]);

        if ($this->first_name) {
            $this->subQuery->andWhere(Db::parseParam('reviewermodule_person.first_name', $this->first_name));
        }

        if ($this->last_name) {
            $this->subQuery->andWhere(Db::parseParam('reviewermodule_person.last_name', $this->last_name));
        }

        if ($this->email) {
            $this->subQuery->andWhere(Db::parseParam('reviewermodule_person.email', $this->email));
        }



        return parent::beforePrepare();
    }
}
