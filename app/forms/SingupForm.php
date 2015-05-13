<?php

namespace Forms;

use Phalcon\Forms\Form,
        Phalcon\Forms\Element\Text,
        Phalcon\Forms\Element\Select;

class SingupForm extends Form
{
        public function initialize()
        {
                $this->add(new Text("telephone"));
        }
}