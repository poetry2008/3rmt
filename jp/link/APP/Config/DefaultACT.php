<?php
$model_Act = FLEA::getSingleton('Model_Act');
/* @var $model_Act Model_Act */
//return $model_Act->getAllAct(),;
return array(
    "BASE" => array(
            "deny" => RBAC_NULL,
            "allow" => RBAC_NULL,
        ),
    "CLASS" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => 'class,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "Create" => array
                        (
                            "allow" =>'class,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "Edit" => array
                        (
                            "allow" =>'class,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "Save" => array
                        (
                            "allow" => 'class,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "Remove" => array
                        (
                            "allow" => 'class,admin',
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NO_ROLE,
            "allow" => 'operator,admin,class',
        ),

    "CUSTOM" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Save" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "ClearCookie" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NULL,
            "allow" => RBAC_NULL,
        ),

    "FREQUENTCLASS" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Add" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Edit" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "EditDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Del" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NO_ROLE,
            "allow" => 'frequentclass,admin',
        ),

    "FREQUENTSITE" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Add" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Edit" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "EditDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Del" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NO_ROLE,
            "allow" => 'frequentsite,admin',
        ),

    "FREQUENTSPECIAL" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Add" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Edit" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "EditDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Del" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NO_ROLE,
            "allow" => 'frequentspecial,admin',
        ),

    "FREQUENTSPECIAL2" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Add" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Edit" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "EditDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Del" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NO_ROLE,
            "allow" => 'frequentspecial2,admin',
        ),

    "GLOBAL" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Update" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NO_ROLE,
            "allow" => 'admin',
        ),

    "GLOBALMANAGER" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Update" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Add" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "AddDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "DeleteDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NO_ROLE,
            "allow" => 'admin',
        ),

    "SEARCH" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Search" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NULL,
            "allow" => RBAC_NULL,
        ),

    "SITE" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Class" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "List" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Add" => array
                        (
                            "allow" => 'site,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "Edit" => array
                        (
                            "allow" => 'site,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "EditDo" => array
                        (
                            "allow" => 'site,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "Del" => array
                        (
                            "allow" => 'site,admin',
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NULL,
            "allow" => RBAC_NULL,
        ),

    "SUBMIT" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Submit" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "List" => array
                        (
                            "allow" => 'submit,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "Del" => array
                        (
                            "allow" => 'submit,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "Edit" => array
                        (
                            "allow" => 'submit,admin',
                            "deny" => RBAC_NULL,
                        ),

                    "EditDo" => array
                        (
 							"allow" => 'submit,admin',
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NULL,
            "allow" => RBAC_NULL,
        ),

    "USERMANAGER" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => 'admin',
                            "deny" => RBAC_NULL,
                        ),

                    "create" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "AddUserDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "UserAuthority" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "UserAuthorityDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Save" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "UserRemove" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "UserEdit" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "UpdatePassword" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "UpdatePasswordDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NULL,
            "allow" => 'admin,usermanager',
        ),

    "ACTMANAGER" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "FlushAuth" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "List" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "AuthEdit" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "AuthEditDo" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NO_ROLE,
            "allow" => 'admin',
        ),
    "SEOPLINK" => array
        (
            "actions" => array
                (
                    "Index" => array
                        ( 
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),
                    "Admintop" => array
                        ( 
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),
                ),
            "deny" => RBAC_NO_ROLE,
            "allow" => 'admin',
        ),


    "ADMIN" => array
        (
            "actions" => array
                (
                    "Index" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Login" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "Logout" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "ImgCode" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                    "ChangeCode" => array
                        (
                            "allow" => RBAC_EVERYONE,
                            "deny" => RBAC_NULL,
                        ),

                ),

            "deny" => RBAC_NULL,
            "allow" => RBAC_EVERYONE,
        ),

);
