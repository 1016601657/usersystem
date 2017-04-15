<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Think;

class Page{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 11;// 分页栏每页显示的页数
    public $lastSuffix = true; // 最后一页是否显示总页数

    private $p       = 'p'; //分页参数名
    private $url     = ''; //当前链接URL
    private $nowPage = 1;

    // 分页显示定制
    private $config  = array(
        'header' => '<div class="dataTables_info">共 %TOTAL_ROW% 条记录</div>',
        'prev'   => '上一页',
        'next'   => '下一页',
        'first'  => '1...',
        'last'   => '...%TOTAL_PAGE%',
        'theme'  => '<div class="layui-box layui-laypage layui-laypage-default" style="width:100%"><div class="dataTables_jump"><span style="float: left;width: 130px;">到第%GO%页</span></div><div class="dataTables_paginate paging_full_numbers">%FIRST% %UP_PAGE% %LINK_PAGE% %END% %DOWN_PAGE%</div></div>',
    );

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=10, $parameter = array()) {
        C('VAR_PAGE') && $this->p = C('VAR_PAGE'); //设置分页参数名称
        /* 基础设置 */
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = $listRows;  //设置每页显示行数
        $this->parameter  = empty($parameter) ? $_GET : $parameter;
        $this->nowPage    = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page){
        return str_replace('[PAGE]', $page, $this->url);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show() {
        if(0 == $this->totalRows) return '';

        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
//        $this->url = U(ACTION_NAME, $this->parameter);
        $this->url = $this->clin_page_url($this->parameter); // 生成标准的url
        $url = $this->url($this->nowPage);

        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->lastSuffix && $this->config['last'] = $this->totalPages;

        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ? '<a class="previous paginate_button paginate_button_disabled" href="' . $this->url($up_row) . '">' . $this->config['prev'] . '</a>' : '';

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ? '<a class="next paginate_button paginate_button_disabled" href="' . $this->url($down_row) . '">' . $this->config['next'] . '</a>' : '';

        //第一页
        $the_first = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1){
            $the_first = '<a class="first paginate_button paginate_button_disabled" href="' . $this->url(1) . '">' . $this->config['first'] . '</a>';
        }

        //最后一页
        $the_end = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
            $the_end = '... <a class="end paginate_button paginate_button_disabled" href="' . $this->url($this->totalPages) . '">' . $this->config['last'] . '</a>';
        }

        //数字连接
        $link_page = "";
        $thego = '<select class="form-control" style="width:50%;display:inline;" name="'.$this->varPage.'" onchange="go_page'.$this->p.'(this.value)">';
        for($i = 1; $i <= $this->rollPage; $i++){
            if(($this->nowPage - $now_cool_page) <= 0 ){
                $page = $i;
            }elseif(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
                $page = $this->totalPages - $this->rollPage + $i;
            }else{
                $page = $this->nowPage - $now_cool_page_ceil + $i;
            }
            if($page > 0 && $page != $this->nowPage){

                if($page <= $this->totalPages){
                    $link_page .= '<a class="paginate_button" href="' . $this->url($page) . '">' . $page . '</a>';
                }else{
                    break;
                }
            }else{
                if($page > 0 && $this->totalPages != 1){
                    $link_page .= '<a class="paginate_active">' . $page . '</a>';
                }
            }
//            $thego .= '<option value ="' . $this->url($page) . '" ';
//            $thego .= $i == $this->nowPage ? 'selected="selected"' :'';
//            $thego .= '>'.$i.'</option>';
        }
        for($h = 1; $h <= $this->totalPages; $h++){
            $thego .= '<option value ="' . $this->url($h) . '" ';
            $thego .= $h == $this->nowPage ? 'selected="selected"' :'';
            $thego .= '>'.$h.'</option>';
        }
        $thego .= '</select><script type="text/javascript">
		   function changeURLArg(url,arg,arg_val){ 
		   alert(url);
		   return false;
				var pattern=arg+"=([^&]*)"; 
				var replaceText=arg+"="+arg_val; 
				if(url.match(pattern)){ 
				var tmp="/("+ arg+"=)([^&]*)/gi"; 
				        tmp=url.replace(eval(tmp),replaceText); 
				return tmp; 
				    }else{ 
				if(url.match("[\?]")){ 
				return url+"&"+replaceText; 
				        }else{ 
				return url+"?"+replaceText; 
				        } 
				    } 
				return url+"\n"+arg+"\n"+arg_val; 
			} 
		   function go_page'.$this->p.'(page){
				var listrows = $("#listrows option:selected").val();
				if(page.indexOf("listrows") <= 0){
					if(listrows > 0){
						window.location = page+"&listrows="+listrows;
					}else{
						window.location = page;
					}
				}else{
					window.location = changeURLArg(page,"listrows",listrows);
				}
			}
        </script>';
        //替换分页内容
        $page_str = str_replace(
            array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%',  '%END%','%LINK_PAGE%', '%TOTAL_ROW%', '%TOTAL_PAGE%', '%GO%'),
            array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $the_end,$link_page,  $this->totalRows, $this->totalPages, $thego),
            $this->config['theme']);
        return "{$page_str}";
    }
    private function clin_page_url($parameter){
        $url = U('');
        $url = str_replace('.html', '?', $url);
        foreach ($parameter as $key => $value) {
            $url .= $key.'='.$value.'&';
        }
        $url = substr($url, 0,-1);
        return $url;
    }
}
