<?php
/*
 $Id$
*/


 class osC_CategoryTree {
   var $root_category_id = 0,
       $max_level = 0,
       $data = array(),
       $root_start_string = '',
       $root_end_string = '',
       $parent_start_string = '',
       $parent_end_string = '',
       $parent_group_start_string = '<li class="subcategory_tree"><ul class="subcategory_tree_info">',
       $parent_group_end_string = '</ul></li>',
       $child_start_string = '<li class="subcategory">',
       $child_end_string = '</li>',
       $spacer_string = '',
       $spacer_multiplier = 1,
       $categories_count = 200,
       $i = 0,
       $end = false
   ;
/*--------------------------------------
 功能: OSC分类
 参数: $load_from_database(bool)   加载数据库
 参数: $green (bool)               通过
 返回值: 无 
 -------------------------------------*/
   function osC_CategoryTree($load_from_database = true,$green = false) {
     global $languages_id;
     $site_id = isset($_GET['site_id'])&&$_GET['site_id'] ? $_GET['site_id'] : 0;
//ccdd
    if ($green) {
      $this->categories_count = tep_db_num_rows(tep_db_query("select * from categories_description where site_id='0' and categories_status='0'"));
    } else {
      $this->categories_count = tep_db_num_rows(tep_db_query("select * from categories"));
    }
    if ($green) { 
         $categories_query = tep_db_query("
           select *
           from (
             select c.categories_id, 
                    cd.categories_status, 
                    cd.categories_name, 
                    c.parent_id,
                    cd.site_id,
                    c.sort_order
             from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
             where c.categories_id = cd.categories_id 
               and cd.language_id = '" . (int)$languages_id . "' 
               and cd.categories_status = '0'
             order by cd.site_id DESC
            ) c
            where site_id = '0' or site_id='".$site_id."'
            group by categories_id
            order by parent_id, sort_order, categories_name");
    } else {
         $categories_query = tep_db_query("
           select *
           from (
             select c.categories_id, 
                    cd.categories_status, 
                    cd.categories_name, 
                    c.parent_id,
                    cd.site_id,
                    c.sort_order
             from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
             where c.categories_id = cd.categories_id 
               and cd.language_id = '" . (int)$languages_id . "' 
             order by cd.site_id DESC
            ) c
            where site_id = '0' or site_id='".$site_id."'
            group by categories_id
            order by parent_id, sort_order, categories_name");
    }
         $this->data = array();
         while ($categories = tep_db_fetch_array($categories_query)) {
            // Ultimate SEO URLs compatibility - Chemo
                  # initialize array container for parent_id 
            $p = array();
            tep_get_parent_categories($p, $categories['parent_id']);
            # For some reason it seems to return in reverse order so reverse the array 
            $p = array_reverse($p);

            # Implode the array to get the parent category path
            $cID = (implode('_', $p) ? implode('_', $p) . '_' . $categories['parent_id'] :
            $categories['parent_id']);
                  # initialize array container for category_id 
            $c = array();
            tep_get_parent_categories($c, $categories['categories_id']);
            # For some reason it seems to return in reverse order so reverse the array 
            $c = array_reverse($c);
            # Implode the array to get the full category path
            $id = (implode('_', $c) ? implode('_', $c) . '_' . $categories['categories_id'] :
            $categories['categories_id']);

            $this->data[$cID][$id] = array('name' => $categories['categories_name'], 'count' => 0);
         } // eof While loop
    } //eof Function
/*--------------------------------------------------
 功能: 建立分支 
 参数: $parent_id(string) 父id
 参数: $level(string)     级别
 参数: $filename(string)  文件名
 返回值: HTML文本 (string)
 -------------------------------------------------*/
   function buildBranch($parent_id, $level = 0,$filename='') {
     if($level == 0){
       if($filename == ''){
       $result = '<table width="100%"><tr><td valign="top" width="20%"><ul class="tdul">
       <li class="subcategory"><a href="?cPath=0'.(isset($_GET['site_id'])?('&site_id=' . $_GET['site_id']):'').'"'.(!isset($_GET['cPath']) || $_GET['cPath'] == '0' ? ' class="current_link"' : '').'>トップ</a></li>
       ';
       }else{
       $result = '<table width="100%"><tr><td valign="top" width="20%"><ul class="tdul">
       <li class="subcategory"><a href="'.$filename.'?cPath=0'.(isset($_GET['site_id'])?('&site_id=' . $_GET['site_id']):'').'"'.(!isset($_GET['cPath']) || $_GET['cPath'] == '0' ? ' class="current_link"' : '').'>トップ</a></li>
       ';
       }
     }else{
       $result = $this->parent_group_start_string;
     }

     if (isset($this->data[$parent_id])) {
       //$i = 0;
       foreach ($this->data[$parent_id] as $category_id => $category) {
         $category_link = $category_id;
         $result .= $this->child_start_string;
         if (isset($this->data[$category_id])) $result .= $this->parent_start_string;

         if ($level == 0) $result .= $this->root_start_string;
     
         $result .= str_repeat($this->spacer_string, $this->spacer_multiplier *
             $level) ;
         if($filename==''){
         $result .= '<a href="?'.(isset($_GET['site_id'])?('site_id=' . $_GET['site_id'] . '&'):'').'cPath=' . $category_link . '"'.(isset($_GET['cPath']) && $_GET['cPath'] == $category_link ? ' class="current_link"' : '').'>';
         }else{
         $result .= '<a href="'.$filename.'?'.(isset($_GET['site_id'])?('site_id=' . $_GET['site_id'] . '&'):'').'cPath=' . $category_link . '"'.(isset($_GET['cPath']) && $_GET['cPath'] == $category_link ? ' class="current_link"' : '').'>';
         }
         $result .= $category['name'];
         $result .= '</a>';

         if ($level == 0) $result .= $this->root_end_string;

         if (isset($this->data[$category_id])) $result .= $this->parent_end_string;

         $result .= $this->child_end_string;
         
         //if ($level == 0 && $i != 0 && $i%20 == 0) {


         if (isset($this->data[$category_id]) && (($this->max_level == '0') ||
     ($this->max_level > $level+1))) $result .= $this->buildBranch($category_id,
     $level+1,$filename);

         if ($this->i !== 0 && $this->end === false &&  $this->i%ceil($this->categories_count/5) === 0 ) {
           $this->end = true;
         }
         if ($level === 0 && $this->end === true) {
          $result .= "</ul></td><td valign=\"top\" width=\"20%\"><ul class=\"tdul\">";
          $this->end = false;
         }
         $this->i++;
         //$i++;
       }
     }

     if($level == 0){
       $result .= '</ul></td></tr></table>';
     }else{
       $result .= $this->parent_group_end_string;
     }
     return $result;
   }
/*-----------------------------------------------
 功能: 构建树
 参数: $filename(string) 文件名
 返回值: 分页文本(string)  
 ----------------------------------------------*/
   function buildTree($filename='') {
     return $this->buildBranch($this->root_category_id,0,$filename);
   }
 }

/*----------------------------------------------
 功能: 获取父类 
 参数: $categories(string) 类别
 参数: $categories_id(string) 类别ID
 返回值: 无
 ---------------------------------------------*/
  function tep_get_parent_categories(&$categories, $categories_id) {
    //ccdd
    $parent_categories_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$categories_id . "'");
    while ($parent_categories = tep_db_fetch_array($parent_categories_query)) {
      if ($parent_categories['parent_id'] == 0) return true;
      $categories[sizeof($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        tep_get_parent_categories($categories, $parent_categories['parent_id']);
      }
    }
  }
