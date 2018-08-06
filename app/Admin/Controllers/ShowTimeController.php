<?php

namespace App\Admin\Controllers;

use App\show_time;
use App\code;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

/**
 * 演出时间管理
 * Class ShowTimeController
 * @package App\Admin\Controllers
 */
class ShowTimeController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('演出时间管理');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('演出时间管理');
            $content->description('编辑');

            $content->body($this->form()->edit($id));
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

            $content->header('演出时间管理');
            $content->description('新增');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(show_time::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->column('number', '编号');
            $grid->column('name', '名称');
            $grid->column('type', '类别')->display(function ($value){
                $name = code::where('id',$value)->pluck('code_dsp_name_cn')->first();
                return $name;
            });
            $grid->column('star_date', '开始日期');
            $grid->column('end_date', '结束日期');
            $grid->column('star_time', '开始时间');
            $grid->column('end_time', '结束时间');
            $grid->column('create_id', '创建者')->display(function ($value){
                $name = Administrator::where('id',$value)->pluck('name')->first();
                return $name;
            });
            $grid->column('update_id', '更新者')->display(function ($value){
                $name = Administrator::where('id',$value)->pluck('name')->first();
                return $name;
            });;
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            $grid->disableExport();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(show_time::class, function (Form $form) {
            $form->display('id', '序号');

            $form->text('number','编号')->rules('required|max:10');
            $form->text('name','名称')->rules('required|max:10');
            $form->select('type','类别')->options(function (){
                $name = code::where('type_id',12)->pluck('code_dsp_name_cn','id');
                return $name;
            });
            $form->dateRange('star_date', 'end_date', '日期范围选择');
            $form->timeRange('star_time', 'end_time', '时间范围选择');
            $form->display('update_id', '更新者')->with(function ($value){
                if($value){
                }else{
                    $value=Admin::user()->id;
                }
                $name = Administrator::where('id',$value)->pluck('name')->first();
                return $name;
            });
            $form->display('create_id', '创建者')->with(function ($value){
                if($value){
                }else{
                    $value=Admin::user()->id;
                }
                $name = Administrator::where('id',$value)->pluck('name')->first();
                return $name;
            });

            $form->hidden('create_id')->default(Admin::user()->id);
            //保存前回调
            $form->saving(function (Form $form) {
                $form->update_id =Admin::user()->id;
            });

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
