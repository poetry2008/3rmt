<div id="main-seach" style="text-align:center;">
                <?php echo tep_draw_form('quock_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '','NONSSL', false), 'get')."\n"; ?>
                        
  
     <?php
      $keywords_str = ''; 
      if (isset($HTTP_GET_VARS['keywords'])) {
        $keywords_str = $HTTP_GET_VARS['keywords']; 
        $_SESSION['s_keywords'] = $keywords_str; 
      } else {
        if (isset($_SESSION['s_keywords'])) { 
          $keywords_str = $_SESSION['s_keywords']; 
        }
      }
      ?>
      <?php if(isset($keywords_str)&&$keywords_str!=''){
      ?>
        
              <input class="nav_input" type="text"  id="seach-content" value="<?php echo $keywords_str;?>" name="keywords">
   <div id="seach-bottom" onClick="document.quock_find.submit();"><a href="javascript:void(0);"></a></div>


      <?php
      }else{
      ?>
      <script language="javascript" type="text/javascript">
      function clear_default_search_input(_this){
        _this.value = '';
        $('#default_search_input').attr('class','nav_input');
      }
      </script>

      <input id="seach-content" class="default_nav_input" type="text" value="<?php
      echo INPUT_BELOW_MSG_TEXT ;?>" onfocus="clear_default_search_input(this)" name="keywords">  <div id="seach-bottom" onClick="document.quock_find.submit();"><a href="javascript:void(0);"></a></div>
      <?php } ?>


                   
                        <?php 
                        echo '<input type="hidden" name="search_in_description"
                        value="1">';
                        echo '<input type="hidden" name="inc_subcat" value="1">';
                        echo tep_hide_session_id();
                        ?>  
 <div><a href="javascript:void(0);"id="game-preview" onmouseover="search_over()" onmouseout="search_out()" onClick="fn(this);" ></a></div>


                                        			 </form>
			</div>
<div id="gameid" style="display:none;">
<div class="seach-close"><img src="images/seach_close.png" alt="close" onClick="search_close()"></div>
<?php include('column_left.php');?>

</div>


 
