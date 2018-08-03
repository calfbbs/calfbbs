/**
 * 字符转义
 * @param str
 */
function escapeUrl(str) {
    var arrEntities={'lt':'<','gt':'>','nbsp':' ','amp':'&','quot':'"'};
    return str.replace(/&(lt|gt|nbsp|amp|quot);/ig,function(all,t){return arrEntities[t];});
}

