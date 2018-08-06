<?php

namespace App\Admin\Controllers;

use App\rest_room;
use App\code;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RestRoomController extends Controller
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

            $content->header('厕所管理');
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

            $content->header('厕所管理');
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

            $content->header('厕所管理');
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
        return Admin::grid(rest_room::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->open_status('开放状态')->switch([
                'on'  => ['value' => 1, 'text' => '开放', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ]);;
            $grid->column('number', '编号');
            $grid->column('name', '名称');
            $grid->column('address', '位置');
            $grid->serve_people_facility( '便民设施')->display(function ($value){
                $name = code::whereIn('id',$value)->pluck('code_dsp_name_cn');
                return $name;
            })->label();
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
        return Admin::form(rest_room::class, function (Form $form) {

            $form->display('id', '序号');
            $form->text('number','编号')->rules('required|max:10');
            $form->text('name','名称')->rules('required|max:10');
            $form->text('address','位置')->rules('required|max:30');
            $form->text('poi_name','POI名称')->rules('required|max:20');
            $form->image('poi_icon','POI图标')->uniqueName();
            $form->url('poi_link_url','POI跳转链接');
            $form->text('poi_x_coord','POI X坐标')->rules('required|max:20');
            $form->text('poi_y_coord','POI Y坐标')->rules('required|max:20');
            $form->checkbox('serve_people_facility','便民设施')->options(code::where('type_id',6)->pluck('code_dsp_name_cn','id'));
            $form->switch('open_status','开放状态')->states([
                'on'  => ['value' => 1, 'text' => '开放', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ])->default(1);

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
