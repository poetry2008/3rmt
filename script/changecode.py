#!/usr/bin/python3
# -*- coding: utf-8 -*-
#
# 使用方法：
#   将sourcedir变量的值改成待转换目录，将outputdir变量的值改未输出目录，执行脚本即可。
#
import os
import codecs
import re
import shutil
def mainLogic():
    #add dirs want to copy
    sourcedir = "/home/szn/SF_UPLOAD/axs/"
    outputdir = "/home/szn/SF_UPLOAD/axs-u/"
    #res = re.compile('php$|js$|css$|html$')
    res = re.compile('php$|js$|css$|html$|cgi$|pl$|pm$|lck$|txt$')
    #encode = ['iso-8859-1', 'gbk', 'gb2312', 'Euc-jp','Shift-jis','Utf-8']
    encode = ['ascii','big5 ','big5hkscs ','cp037 ','cp424 ','cp437 ','cp500 ','cp737 ','cp775 ','cp850 ','cp852 ','cp855 ','cp856 ','cp857 ','cp860 ','cp861 ','cp862 ','cp863 ','cp864 ','cp865 ','cp866 ','cp869 ','cp874 ','cp875 ','cp932 ','cp949 ','cp950 ','cp1006 ','cp1026 ','cp1140 ','cp1250 ','cp1251 ','cp1252 ','cp1253 ','cp1254 ','cp1255 ','cp1256 ','cp1257 ','cp1258 ','euc_jp ','euc_jis_2004 ','euc_jisx0213 ','euc_kr ','gb2312 ','gbk ','gb18030 ','hz ','iso2022_jp ','iso2022_jp_1 ','iso2022_jp_2 ','iso2022_jp_2004 ','iso2022_jp_3 ','iso2022_jp_ext ','iso2022_kr ','latin_1 ','iso8859_2 ','iso8859_3 ','iso8859_4 ','iso8859_5 ','iso8859_6 ','iso8859_7 ','iso8859_8 ','iso8859_9 ','iso8859_10 ','iso8859_13 ','iso8859_14 ','iso8859_15 ','johab ','koi8_r ','koi8_u ','mac_cyrillic ','mac_greek ','mac_iceland ','mac_latin2 ','mac_roman ','mac_turkish ','ptcp154 ','shift_jis ','shift_jis_2004 ','shift_jisx0213 ','utf_32 ','utf_32_be ','utf_32_le ','utf_16 ','utf_16_be ','utf_16_le ','utf_7 ','utf_8 ','utf_8_sig','','idna ','mbcs ','palmos ','punycode ','raw_unicode_escape ','undefined ','unicode_escape ','unicode_internal']
    #print(sourcedir)

    for parent ,dirnames,filenames in os.walk(sourcedir):
        for filename in filenames:
            fullname =  os.path.join(parent,filename)
            backupfullname = fullname.replace(sourcedir,outputdir)
            if (len(res.findall(filename))):
                errorCount = 0
                for encodetmp in encode:
                    try:
                        change2utf8(fullname,backupfullname,encodetmp)
#                        print (fullname + 'SSSSSSSSSS')
                        break
                    except  UnicodeDecodeError as e:
                        errorCount  = errorCount +1
                        if errorCount == len(encode) :
                            print (fullname + 'FFFFFFFF')
                            print (e)
                        continue
            else:
                dirname = os.path.dirname(backupfullname)
                if os.path.exists(dirname) == False:
                    os.makedirs(dirname)
                shutil.copyfile(fullname,backupfullname)
                

                    
def change2utf8(infilename,outfilename,encode):
    srcfile = codecs.open(infilename,'r',encode)
    if not os.path.isdir(os.path.split(outfilename)[0]):
        os.makedirs(os.path.split(outfilename)[0])
    tofile   = codecs.open(outfilename,'w','Utf-8')
    tofile.writelines(srcfile.readlines())

if __name__ == '__main__':
    mainLogic()
