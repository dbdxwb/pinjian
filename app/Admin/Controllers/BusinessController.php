<?php

namespace App\Admin\Controllers;

use App\business;
use App\code;

use App\hot_spot;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

/**
 * 商铺管理
 * Class BusinessController
 * @package App\Admin\Controllers
 */
class BusinessController extends Controller
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

            $content->header('商家管理');
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

            $content->header('商家管理');
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

            $content->header('商家管理');
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
        return Admin::grid(business::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->business_status('营业状态')->switch([
                'on'  => ['value' => 1, 'text' => '营业', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '休业', 'color' => 'default'],
            ]);;
            $grid->column('number', '编号');
            $grid->column('name', '名称');
            $grid->column('营业时间')->display(function () {
                return $this->star_time . '-' . $this->end_time;
            });;
            $grid->food_type( '商家风格')->display(function ($value){
                $name = code::whereIn('id',$value)->pluck('code_dsp_name_cn');
                return $name;
            })->label();
            $grid->hot_status('加入热点')->switch([
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ]);
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
        return Admin::form(business::class, function (Form $form) {

            $form->display('id', '序号');

            $form->text('number','编号')->rules('required|max:10');
            $form->text('name','名称')->rules('required|max:10');
            $form->text('address','位置')->rules('required|max:30');
            $form->text('poi_name','POI名称')->rules('required|max:20');
            $form->image('poi_icon','POI图标')->uniqueName();
            $form->url('poi_link_url','POI跳转链接');
            $form->text('poi_x_coord','POI X坐标')->rules('required|max:20');
            $form->text('poi_y_coord','POI Y坐标')->rules('required|max:20');
            $form->timeRange('star_time', 'end_time', '选择营业时间');
            $form->currency('price','人均价位')->symbol('￥')->rules('required|max:20');
            $form->textarea('shop_intro','商家简介')->rules('required|max:120');
            $form->textarea('shop_info','商家描述')->rules('required|max:120');

            $form->checkbox('food_type','商家风格')->options(code::where('type_id',7)->pluck('code_dsp_name_cn','id'));
            $form->checkbox('serve_facility','服务设施')->options(code::where('type_id',3)->pluck('code_dsp_name_cn','id'));
            $form->switch('hot_status','加入热点')->states([
                'on'  => ['value' => 1, 'text' => '加入', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '取消', 'color' => 'default'],
            ])->default(2);

            $form->switch('self_support','是否自营')->states([
                'on'  => ['value' => 1, 'text' => '自营', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '他营', 'color' => 'default'],
            ])->default(1);
            $form->switch('business_status','营业状态')->states([
                'on'  => ['value' => 1, 'text' => '营业', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '休业 ', 'color' => 'default'],
            ])->default(1);

            $form->multipleFile('pics','宣传照片')->removable()->uniqueName();

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

            //保存后回调
            $form->saved(function (Form $form) {
                $hot_status=$form->model()->hot_status;
                if($hot_status==1){
                    //创建-同时创建热点表
                    //查询该数据是否存在
                    $hot_spot=hot_spot::where('type','=',2)->where('link_id','=',$form->model()->id)->first();
                    if(!$hot_spot){
                        $hot_id=$this->hotSpotSave($form);
                    }
//                    else{
//                        //删除-同时删除热点表
//                        if ($_POST['_method']=='DELETE'){
//                            $this->hotSpotDelete($form);
//                        }
//                    }
                }elseif($hot_status==0){
                    $this->hotSpotDelete($form);
                }
            });

        });
    }

    public function hotSpotSave(Form $form){
        $hotSpot=new hot_spot();
        $hotSpot->open_status=1;
        $hotSpot->number=$form->model()->number;
        $hotSpot->name=$form->model()->name;
        $hotSpot->address=$form->model()->address;
        $hotSpot->hot_info=$form->model()->shop_intro;
        $hotSpot->star_date=date('y-m-d');
        $hotSpot->end_date=date('y-m-d');
        $hotSpot->star_time=$form->model()->star_time;
        $hotSpot->end_time=$form->model()->end_time;
        $hotSpot->pics=$form->model()->pics;
        $hotSpot->type=2;
        $hotSpot->link_id=$form->model()->id;
        $hotSpot->update_id=$form->model()->update_id;
        $hotSpot->create_id=$form->model()->create_id;
        $hotSpot->save();
        return $hotSpot->id;
    }


    public function hotSpotDelete(Form $form){
        $return=hot_spot::where('type','=',2)->where('link_id','=',$form->model()->id)->delete();
        return $return;
    }
}
