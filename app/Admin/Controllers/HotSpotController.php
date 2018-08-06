<?php

namespace App\Admin\Controllers;

use App\hot_spot;
use App\code;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

/**
 * 热点管理
 * Class HotSpotController
 * @package App\Admin\Controllers
 */
class HotSpotController extends Controller
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

            $content->header('热点管理');
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

            $content->header('热点管理');
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

            $content->header('热点管理');
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
        return Admin::grid(hot_spot::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->open_status('开放状态')->switch([
                'on'  => ['value' => 1, 'text' => '开放', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '未开放', 'color' => 'default'],
            ]);
            $grid->column('number', '编号');
            $grid->column('name', '名称');
            $grid->column('address', '位置');
            $grid->column('type', '类别')->display(function ($value){
                if($value==1){
                    $name="<span class='label bg-yellow'>饭店自动</span>";
                }elseif($value==2){
                    $name="<span class='label bg-yellow'>商铺自动</span>";
                }else{
                    $name="<span class='label bg-yellow'>手动添加</span>";
                }
                return $name;
            });
            $grid->column('开放日期')->display(function () {
                return $this->star_date . '-' . $this->end_date;
            });
            $grid->column('开放时间')->display(function () {
                return $this->star_time . '-' . $this->end_time;
            });
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(hot_spot::class, function (Form $form) {

            $form->display('id', '序号');
            $form->text('number','编号')->rules('required|max:10');
            $form->text('name','名称')->rules('required|max:10');
            $form->text('address','位置')->rules('required|max:30');
            $form->dateRange('star_date', 'end_date', '选择日期范围');
            $form->timeRange('star_time', 'end_time', '选择营业时间');
            $form->textarea('hot_info','商家简介')->rules('required|max:120');
            $form->switch('open_status','开放状态')->states([
                'on'  => ['value' => 1, 'text' => '开放', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '未开放', 'color' => 'default'],
            ])->default(1);
            $form->multipleFile('pics','宣传照片')->removable();

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
