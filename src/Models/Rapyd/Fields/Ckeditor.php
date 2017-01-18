<?php

namespace Wbe\Crud\Models\Rapyd\Fields;

use Collective\Html\FormFacade as Form;
use Zofe\Rapyd\Rapyd;

class Ckeditor extends \Zofe\Rapyd\DataForm\Field\Field
{
    public $type = "text";

    /**
     * Генерація Rapyd поля з HTML редактором Ckeditor
     */
    public function build()
    {
        $output = "";
        if (parent::build() === false) return;

        switch ($this->status) {
            case "disabled":
            case "show":

                if ($this->type == 'hidden' || $this->value == "") {
                    $output = "";
                } elseif ((!isset($this->value))) {
                    $output = $this->layout['null_label'];
                } else {
                    $output = nl2br(htmlspecialchars($this->value));
                }
                $output = "<div class='help-block'>" . $output . "&nbsp;</div>";
                break;

            case "create":
            case "modify":

//        Rapyd::js('redactor/jquery.browser.min.js');
//        Rapyd::js('redactor/redactor.min.js');
//        Rapyd::css('redactor/css/redactor.css');

                Rapyd::js('ckeditor/ckeditor.js');

                $output = Form::textarea($this->name, $this->value, $this->attributes);
                //Rapyd::script("$('[id=\"".$this->name."\"]').redactor();");
                //Rapyd::script("CKEDITOR.replace( '" . $this->name ."' )");
                Rapyd::script("CKEDITOR.replace( '" . $this->name . "' )");

                break;

            case "hidden":
                $output = Form::hidden($this->name, $this->value);
                break;

            default:
                ;
        }
        $this->output = "\n" . $output . "\n" . $this->extra_output . "\n";
    }

}
