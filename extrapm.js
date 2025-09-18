var grp_id = 0;
var bEditing = false;

function Newchat( userid ) {
    $.ajax({
        url   : "action.php",
        method: "POST",
        data  :{
            user_id: userid,
            chatname: document.getElementById("newgroupname").value,
            action : 'newchat'
        },
        success:function( data ) {
            let d = JSON.parse( data );
            if( d["chatid"] && d["chatid"] !== undefined ) openChat( d["chatid"], userid );
        }
    });
}

function Shownewgrpnl() {
    let div = document.getElementById( "newchat" );
    
    div.style.display = "block";
}

function Showadduserpnl() {
    document.getElementById("user_list2").style.display = "block";
    document.getElementById("chatarea").style.display = "none";
    document.getElementById("groupchatlist").style.display = "none";
}

function Addusertogc( userid ) {
    $.ajax({
        url   : "action.php",
        method: "POST",
        data  :{
            chat_id: grp_id,
            user_id: userid,
            action : 'addutogc'
        },
        success:function( data ) {
            let d = JSON.parse( data );
            if( d[1] == 1 ) {
                document.getElementById("user_list2").style.display = "none";
                openChat( grp_id, userid );
            }
            else {
                alert( "Пользователь уже является участником данного чата." );
            }
        }
    });
}

function displayChat( dispdata, userid ) {
    let data = JSON.parse( dispdata );
    let div = null; let elem = document.getElementById( "messages_area" );
    let bgclass = "row justify-content-end";
    let elem_msg = document.getElementById( "messages_area" );

    for( let i = 0; i < elem_msg.children.length; i++ ) elem_msg.children[i].remove();

    document.getElementById("groupchatlist").style.display = "none";
    document.getElementById("chatarea").style.display = "block";
    document.getElementById("chatname").innerHTML = "<h3>"+data[0]["title"]+"</h3>";

    for( let i = 0; i < data.length; i++ ) {
        if( data[i]["group_chat_message"] === undefined || !data[i]["group_chat_message"] ) continue;

        div = document.createElement( "div" );
        if( data[i]["userid"] == userid ) {
            bgclass = "text-dark alert-light";
            div.className = "row justify-content-start";
        }
        else {
            bgclass = "alert-success";
            div.className = "row justify-content-end";
        }
        div.innerHTML = '<div onmouseenter=\'Showediticons('+data[i]["id"]+', this.children[0]);\' onmouseleave=\'Hideicons( '+data[i]["id"]+' );\' class="col-sm-10"><div class="shadow-sm alert '+bgclass+'"><b>'+data[i]["uname"]+' - </b><span>'+data[i]["group_chat_message"]+'</span><br /><div class="text-right"><small><i>'+data[i]["created_on"]+'</i></small></div></div></div>';
        elem.appendChild( div );
    }
}

function openChat( chatid, userid ) {
    $.ajax({
        url   : "action.php",
        method: "POST",
        data  :{
            chat_id: chatid,
            user_id: userid,
            action : 'getchat'
        },
        success:function( data ) {
            grp_id = chatid;
            displayChat( data, userid );
        }
    });
}

function Resetview( ) {
    document.getElementById("user_list2").style.display = "none";
    document.getElementById("chatarea").style.display = "none";
    document.getElementById("groupchatlist").style.display = "block";
}

function sendGroupMessage( chatid, user_id, message ) {
    $.ajax({
        url   : "action.php",
        method: "POST",
        data  :{
            group_chat_message: message,
            groupid: chatid,
            userid: user_id,
            action : 'sendgrpmsg'
        },
        success:function( data ) {
            displayChat( data, user_id );
        }
    });
}

function SaveEdited( id ) {
    $.ajax({
        url   : "action.php",
        method: "POST",
        data  :{
            msg: document.getElementsByClassName("msgedit-input")[0].value,
            msgid: id,
            action : 'updatemsgp'
        },
        success:function( data ) {
            let el = document.getElementsByClassName("msgedit")[0].parentElement.children[2];
            el.textContent = document.getElementsByClassName("msgedit-input")[0].value;
            document.getElementsByClassName("msgedit")[0].remove();
            el.style.display = "revert";
            bEditing = false;
            Hideicons( id );
        }
    });
}

function Editmsg( id ) {
    let icons = document.getElementById( "icons-"+id );
    if( !icons || icons === null ) return;

    bEditing = true;
    let msg = icons.parentElement.children[2];
    msg.style.display = "none";
    let edit = document.createElement( "div" );
    edit.className = "msgedit";
    edit.innerHTML = "<input type=\"text\" class=\"msgedit-input\" value=\""+msg.textContent+"\" /><a onclick=\"SaveEdited("+id+");\" href=\"#\">Сохранить</a>";
    msg.after( edit );
}

function Removemsg( id ) {
    $.ajax({
        url   : "action.php",
        method: "POST",
        data  :{
            msgid: id,
            action : 'removemsgp'
        },
        success:function( data ) {
            let icons = document.getElementById( "icons-"+id );
            icons.parentElement.remove();
            bEditing = false;
        }
    });
}

function Showediticons( id, parent ) {
    if( bEditing ) return;

    let icons = document.createElement( "div" );
    icons.id = "icons-"+id;

    icons.innerHTML = "<span onclick='Editmsg("+id+");'>&#x270E;</span><span onclick='Removemsg("+id+");'>&#x274C;</span>";
    parent.children[0].before( icons );
}

function Hideicons( id ) {
    if( bEditing ) return;

    let icons = document.getElementById( "icons-"+id );

    if( icons && icons !== null ) icons.remove();
}