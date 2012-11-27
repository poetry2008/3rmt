<?php
function _T($key, $language = '')
{
    static $instance = null;
    if (!isset($instance['obj'])) {
        $instance = array();
        $obj =& FLEA::getSingleton('FLEA_Language');
        $instance = array('obj' => & $obj);
    }
    return $instance['obj']->get($key, $language);
}

/**
 * 载入语言字典文件
 *
 * @param string $dictname
 * @param string $language 指定为 '' 时表示将字典载入默认语言包中
 * @param boolean $noException
 *
 * @return boolean
 */
function load_language($dictname, $language = '', $noException = false)
{
    static $instance = null;
    if (!isset($instance['obj'])) {
        $instance = array();
        $obj =& FLEA::getSingleton('FLEA_Language');
        $instance = array('obj' => & $obj);
    }
    return $instance['obj']->load($dictname, $language, $noException);
}

/**
 * FLEA_Language 提供了语言转换功能
 */
class FLEA_Language
{
    /**
     * 保存当前载入的字典
     *
     * @var array
     */
    var $_dict = array();

    /**
     * 指示哪些语言文件已经被载入
     *
     * @var array
     */
    var $_loadedFiles = array();

    /**
     * 构造函数
     *
     * @return FLEA_Language
     */
    function FLEA_Language()
    {
        $autoload = FLEA::getAppInf('autoLoadLanguage');
        if (!is_array($autoload)) {
            $autoload = explode(',', $autoload);
        }
        foreach ($autoload as $load) {
            $load = trim($load);
            if ($load != '') {
                $this->load($load);
            }
        }
    }

    /**
     * 载入指定语言的字典文件
     *
     * 所有的语言文件均按照“语言/字典名.php”的形式保存在由应用程序设置
     * 'languageFilesDir' 指定的目录中。默认的保存目录为 FLEA/Languages。
     *
     * 如果没有指定 $language 参数，则载入由应用程序设置 'defaultLanguage'
     * 指定的语言目录下的文件。
     *
     * $language 和 $dicname 参数均只能使用 26 个字母、10 个数字
     * 和 “-”、“_” 符号。并且为全小写。
     *
     * @param string $dictname 字典名，例如 'fleaphp'、'rbac'
     * @param string $language 指定为 '' 时表示将字典载入默认语言包中
     * @param boolena $noException
     */
    function load($dictname, $language = '', $noException = false)
    {
        $dictnames = explode(',', $dictname);
        foreach ($dictnames as $dictname) {
            $dictname = trim($dictname);
            if ($dictname == '') { continue; }

            $dictname = preg_replace('/[^a-z0-9\-_]+/i', '', strtolower($dictname));
            $language = preg_replace('/[^a-z0-9\-_]+/i', '', strtolower($language));
            if ($language == '') {
                $language = FLEA::getAppInf('defaultLanguage');
                $default = true;
            } else {
                $default = false;
            }

            $filename = FLEA::getAppInf('languageFilesDir') . DS .
                $language . DS . $dictname . '.php';
            if (isset($this->_loadedFiles[$filename])) { continue; }

            if (is_readable($filename)) {
                $dict = require($filename);
                $this->_loadedFiles[$filename] = true;
                if (isset($this->_dict[$language])) {
                    $this->_dict[$language] = array_merge($this->_dict[$language], $dict);
                } else {
                    $this->_dict[$language] = $dict;
                }
                if ($default) {
                    $this->_dict[0] =& $this->_dict[$language];
                }
            } else if (!$noException) {
                FLEA::loadClass('FLEA_Exception_ExpectedFile');
                return __THROW(new FLEA_Exception_ExpectedFile($filename));
            }
        }
    }

    /**
     * 返回指定键的对应语言翻译，没有找到翻译时返回键
     *
     * @param string $key
     * @param string $language 指定为 '' 时表示从默认语言包中获取翻译
     *
     * @return string
     */
    function get($key, $language = '')
    {
        if ($language == '') { $language = 0; }
        return isset($this->_dict[$language][$key]) ?
            $this->_dict[$language][$key] :
            $key;
    }
}
