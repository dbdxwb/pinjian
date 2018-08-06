<?php

namespace App\Admin\Controllers;

use App\bus;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

/**
 * 公交管理
 * Class BusController
 * @package App\Admin\Controllers
 */
class BusController extends Controller
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

            $content->header('公交管理');
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

            $content->header('公交管理');
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

            $content->header('公交管理');
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
        return Admin::grid(bus::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->run_status('运行状态')->switch([
                'on'  => ['value' => 1, 'text' => '运行', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '停运', 'color' => 'default'],
            ]);;
            $grid->column('number', '编号');
            $grid->column('name', '名称');
            $grid->column('name', '名称');
            $grid->column('运行时间')->display(function () {
                return $this->star_time . '-' . $this->end_time;
            });;

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
        return Admin::form(bus::class, function (Form $form) {

            $form->display('id', '序号');

            $form->text('number','编号')->rules('required|max:10');
            $form->text('name','名称')->rules('required|max:10');
            $form->text('address','位置')->rules('required|max:30');
            $form->timeRange('star_time', 'end_time', '选择运行时间');

            $form->switch('run_status','加入热点')->states([
                'on'  => ['value' => 1, 'text' => '运行', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '停运', 'color' => 'default'],
            ])->default(1);

            $form->multipleFile('pics','公交照片')->removable()->uniqueName();

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
