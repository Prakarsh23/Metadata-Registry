<?php

namespace App\Http\Omr\Controllers;

use App\Models\Concept;
use App\Models\Vocabulary;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class ConceptController extends OmrController

{
  use ModelForm;

  /**
   * Index interface.
   *
   * @param Vocabulary $vocabulary
   *
   * @return Content
   */
  public function index(Vocabulary $vocabulary)
  {
    return Admin::content(function (Content $content) use ($vocabulary) {
      $content->header('Vocabulary:');
      $content->description($vocabulary->name);
      $content->body($this->grid($vocabulary));
    });
  }

  /**
   * Edit interface.
   *
   * @param $id
   *
   * @return Content
   */
  public function edit($id)
  {
    return Admin::content(function (Content $content) use ($id) {
      $content->header('header');
      $content->description('description');
      $content->body($this->form()
                          ->edit($id));
    });
  }

  /**
   * Create interface.
   *
   * @return Content
   */
  public function create()
  {
    return Admin::content(function (Content $content) {
      $content->header('header');
      $content->description('description');
      $content->body($this->form());
    });
  }

  public function select(Request $request)
  {
    $q = $request->get('q');
    return Concept::where('vocabulary_id',$q)->whereNotNull('pref_label')->orderBy('pref_label')->get(['id', 'pref_label'])
        ->mapWithKeys(function ($item) {
          return [ $item['id'] => $item['pref_label'] ];
        })->toJson();

  }

  /**
   * Make a grid builder.
   *
   * @return Grid
   */
  protected function grid($vocabulary)
  {
    return Admin::grid(Concept::class,
        function (Grid $grid) use ($vocabulary) {
          $grid->model()
               ->where('vocabulary_id', $vocabulary->id);
          $grid->disableActions()
               ->disableCreation()
               ->disableExport()
               ->disableRowSelector();
          $grid->column('pref_label', 'Name')
               ->display(function ($name) {
                 return '<a href="' . url('concepts/' . $this->id) . '">' . $name . '</a>';
               })
               ->sortable();
          $grid->language();
          $grid->column('status.display_name', 'Status');
          $grid->created_at('Created')
               ->datediff()
               ->sortable();
          $grid->updated_at('Last Updated')
               ->sortable()
               ->datediff();
        });
  }

  /**
   * Make a form builder.
   *
   * @return Form
   */
  protected function form()
  {
    return Admin::form(Concept::class,
        function (Form $form) {
          $form->display('id', 'ID');
          $form->display('created_at', 'Created At');
          $form->display('updated_at', 'Updated At');
        });
  }
}