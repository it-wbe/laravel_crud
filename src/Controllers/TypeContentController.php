<?php

namespace Wbe\Crud\Controllers;

use Zofe\Rapyd\Facades;
use Zofe\Rapyd\Rapyd;
use Zofe\Rapyd\DataEdit\DataEdit;
use Zofe\Rapyd\DataForm\DataForm;
use Zofe\Rapyd\DataForm\Field\Field;

use App\Models\ContentTypes\Game;
use Wbe\Crud\Models\Type;
use App\Models\ContentTypes\Hints;
use App\Models\ContentTypes\HintModel;
use App\Models\ContentTypes\Hints\Total;
use App\Models\ContentTypes\Markets;
use App\Models\ContentTypes\Samples;
use App\Models\ContentTypes\Spans;
use App\Http\Controllers\Controller;

class TypeContentController extends Controller
{
    public function index()
    {
        $form = DataForm::source(new Type);
        $form->label('From Type Content');
        $form->add('table','Table Name', 'text');

        $form->submit('Save');

        $form->saved(function () use ($form) {
            $form->message("record saved");
            $form->link("admin/","admin");
        });

        return $form->view('crud::crud.layout', compact('form'));
    }
}
