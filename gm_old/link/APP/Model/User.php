<?php
FLEA::loadClass('FLEA_Rbac_UsersManager');
class Model_User extends FLEA_Rbac_UsersManager
{
  var $tableName = 'gm_users';
  var $primaryKey = 'user_id';
  var $rolesFields = 'roles';
  var $usernameField = 'username';

  var $manyToMany = array(
  'tableClass' => 'Model_Role',
  'mappingName' => 'roles',
  'joinTable' => 'gm_roles_users',
  );
  /**
   * 创建用户
   * 过程：查看是否有当前用户名及组名，如果有返回错误
   * 如果没有，新建用户，并添加到组
   *
   * @param unknown_type $row
   * @return unknown
   */
  //  function create($row){
  //    if(!array_key_exists("username",$row) or !array_key_exists("password",$row)){
  //      //出现错误,入口参数不正确,只有程序员才可能出现。
  //      echo "入口参数错误";
  //      return false;
  //    }
  //    if($this->existsUsername($row['username'])){
  //      //出现错误 用户存在
  //      echo "用户存在";
  //      return false;
  //    }else{
  //      $row['roles']=array(array("rolename"=>$row['username']));
  //      __TRY();
  //      $result = parent::create($row);
  //      $ex = __CATCH();
  //      if (__IS_EXCEPTION($ex)) {
  //        dump($ex);
  //        return false;
  //      } else {
  //        //成功
  //        return true;
  //      }
  //    }
  //
  //
  //
  //  }

  function login($username, $password) {
    // 验证用户名和密码是否正确
    $user = $this->findByUsername($username);
    if (!$user || !$this->checkPassword($password, $user[$this->passwordField])) {
      return false;
    }
//    dump($user);
    $role_user = $user['roles'];
//    exit;
    foreach ($role_user as $key=>$value){
      if($value['rolename']=="admin" or $value['rolename']=="operator"){
        $loginable = true;
      }
    }
    if(!$loginable){
      return false;
    }
    // 获取用户角色信息
    $roles = $this->fetchRoles($user);
    
    // 获得 FLEA_Com_RBAC 组件实例
    $rbac = & FLEA::getSingleton('FLEA_Com_RBAC');
    /* @var $rbac FLEA_Com_RBAC */

    /* @var $rbac FLEA_Com_RBAC */

    // 为了降低服务器负担，我们只在 session 中存储用户ID和用户名
    $sessionUser = array(
    'USERID' => $user[$this->primaryKey],
    'USERNAME' => $user[$this->usernameField],
    );

    // 将用户ID、用户名和角色信息保存到 session
    $rbac->setUser($sessionUser, $roles);

    // 登录成功
    return true;

  }
//  function save(& $row, $saveLinks = true, $updateCounter = true)
//  {
//
//    if (empty($row[$this->primaryKey])) {
//      return $this->create($row, $saveLinks, $updateCounter);
//    } else {
//      //return $this->update($row, $saveLinks, $updateCounter);
//      
//    }
//  }
}

