function toDateTimeString(datetime) {
    return ""
    + (datetime.getFullYear())
    + '/'
    + ( "00" + (datetime.getMonth()+1) ).substring(( "00" + (datetime.getMonth()+1) ).length-2)
    + '/'
    + ( "00" + (datetime.getDate())    ).substring(( "00" + (datetime.getDate())    ).length-2)
    + ' '
    + ( "00" + (datetime.getHours())   ).substring(( "00" + (datetime.getHours())   ).length-2)
    + ':'
    + ( "00" + (datetime.getMinutes()) ).substring(( "00" + (datetime.getMinutes()) ).length-2)
    + ':'
    + ( "00" + (datetime.getSeconds()) ).substring(( "00" + (datetime.getSeconds()) ).length-2)
}
