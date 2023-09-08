/**
 * HTML templates for the discussions.
 * Don't put logic here (conditions...), put them in scripts.js
 */


/**
 * Builds the HTML to notify a new discussion in the notification block
 */
function htmlDiscussionNotif(topicTitle, date, url, authorId, authorPseudo, lastMessage) {
    
    authorPseudo = (authorPseudo==="") ? "Membre#"+authorId : authorPseudo;
    return '<a href="'+ url +'" target="_blank" class="notif">\
                &#x1F5E8;&#xFE0F; <strong>'+ authorPseudo +'</strong> a répondu à <span style="color:darkred">'+ topicTitle +'</span>\
                <div class="extract">« '+lastMessage+'<span style="color:darkred">...</span> »</div>\
                <div class="date">'+ date +'</div>\
            </a>';
}


function htmlDiscussion(topicId, topicType, topicTitle, firstMessage, lastMessage, nbrReplies) {
    
    var url = urlDiscussion(topicId, lastMessage.message_id);
    var otherMessagesLink = (nbrReplies>1) ? '<a id="loadDiscussion'+topicId+'" class="link_other_messages" onclick="loadDiscussion('+topicId+')">··· voir les '+(nbrReplies-1)+' autres réponses ···</a>' : '';
    // If there is no reply, the last message is the same as the first one, 
    // so don't display it two times.
    htmlLastMessage = (nbrReplies === 0) ? "" : htmlDiscussionMessage(lastMessage.message, lastMessage.is_json, 
                                                                      lastMessage.author_pseudo, lastMessage.datetime_utc, 
                                                                      nbrReplies+1);
    
    return '<div class="topic '+topicType+'">\
                <h3 onclick="toggle(\'replies'+topicId+'\')">\
                    <span style="font-weight:normal">&#x1F4AC;</span> '+topicTitle+'\
                </h3>\
                <div id="replies'+topicId+'">\
                    '+htmlDiscussionMessage(firstMessage.message, firstMessage.is_json, firstMessage.author_pseudo, firstMessage.datetime_utc, 1)+'\
                    '+otherMessagesLink+'\
                    '+htmlLastMessage+'\
                </div>\
                <div class="reply_button">\
                    <a id="replyButton'+topicId+'" href="#" onclick="display(\'sendform'+topicId+'\');this.style.display=\'none\';document.querySelector(\'#message'+topicId+'\').focus()">\
                        Commenter\
                    </a>\
                    <form id="sendform'+topicId+'" method="post" action="" onsubmit="replyDiscussion('+topicId+', '+(nbrReplies+1)+'); return false;">\
                        <div id="replyError'+topicId+'"></div>\
                        <textarea id="message'+topicId+'" placeholder="Écrivez votre réponse ici"></textarea>\
                        <input type="submit" value="Envoyer">\
                    </form>\
                </div>\
            </div>';
}


/**
 * 
 * @param {string} message
 * @param {string} isJson If "true", the message must be treated as a JSON string
 *                        storing the data of a game event (ex: an agression).
 *                        If "false", it's an ordinary textual message posted by a player.
 * @param {string} pseudo
 * @param {string} utcDate The date when the message was posted, in the ISO format
 * @param {int} replyNum The number of order of the message in the discussion (1, 2, 3...)
 * @returns {String}
 */
function htmlDiscussionMessage(message, isJson, pseudo, utcDate, replyNum) {
    
    let formattedMessage = "";
    
    if(isJson === 1) {
        // If the message is JSON-formatted (raw data of an event: agression...)
        formattedMessage = htmlEventTemplate(JSON.parse(message));
    }
    else {
        // If the message is an ordinary textual message (written by a player)
        formattedMessage = nl2br(message);
        

    }
    
    return '<div class="message">\
            <div class="reply_num">#'+replyNum+'</div>\
            <div class="pseudo">&#x1F464; <strong>'+pseudo+'</strong></div>\
            <div class="time" title="Fuseau horaire de Paris">'+dateIsoToString(utcDate)+'</div>\
            <div class="text">'+formattedMessage+'</div>\
        </div>';
}


function htmlEvent(title, message, dateString, iAmInvolved) {
    
    var classInvolved = (iAmInvolved === true) ? "iAmInvolved" : "iAmNotInvolved";
    
    return '<div class="topic event '+classInvolved+'">\
            <h3>'+title+'</h3>\
            <div class="message">\
                <div class="text">\
                   '+message+'\
                </div>\
                <div class="time" title="Fuseau horaire de Paris">\
                    <a href="#">Commenter</a> · '+dateString+'\
                </div>\
            </div>\
        </div>';
}


function htmlEventTemplate(apiDatas) {
    
    var coords = apiDatas.datas.coord_x+":"+apiDatas.datas.coord_y;
    
    if (apiDatas.event_alias === "heal_citizen") {
        return ("&#x1F489; <strong>"+apiDatas.datas.author.citizen_pseudo+"</strong> a soigné la blessure\
                         de <strong>"+apiDatas.datas.target.citizen_pseudo+"</strong>\
                         en zone "+coords+".");
    }
    else if (apiDatas.event_alias === "attack_citizen") {
        return ("&#x1F44A;&#x1F3FC; <strong>"+apiDatas.datas.author.citizen_pseudo+"</strong>\
                         a agressé <strong>"+apiDatas.datas.target.citizen_pseudo+"</strong>\
                         en zone "+coords+" !");
    }
    else {
        return ("<strong class=\"red\">[BUG] Evénement non prévu - Signalez-le \
                au développeur du jeu...</strong>");
    }
}


function htmlNewDiscussionForm(citizenPseudo)
{

    var fieldPseudoStyle = (citizenPseudo === "") ? '' : 'display:none';

    return '<div id="newDiscussion"></div>\
        <input id="buttonNewTopic" type="text" placeholder="Ajouter un message...">\
        <div id="send" class="topic discuss" style="display:none">\
            <div class="message">\
                <form id="sendform">\
                    <div>\
                        <div id="errorNewTopicPseudo" class="red"></div>\
                        <input id="guestPseudo" type="text" name="guest_pseudo" placeholder="Votre pseudo" style="'+fieldPseudoStyle+'">\
                    </div>\
                    <div id="errorNewTopicMessage" class="red"></div>\
                    <input type="text" id="titleNew" placeholder="Titre de la discussion (facultatif)">\
                    <textarea id="messageNew" placeholder="Donnez votre avis sur les stratégies ou demandez de l\'aide..."></textarea>\
                    <input type="submit" value="Envoyer"><br>\
                    <a href="#" id="hideSendform">Annuler</a>\
                </form>\
            </div>\
        </div>';
}
