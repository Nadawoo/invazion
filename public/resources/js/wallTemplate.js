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


function htmlDiscussion(topicId, topicType, topicTitle, firstMessage, lastMessage, nbrOtherMessages) {
    
    var url = urlDiscussion(topicId, lastMessage.message_id);
    var otherMessagesLink = (nbrOtherMessages>1) ? '<a id="loadDiscussion'+topicId+'" class="link_other_messages" onclick="loadDiscussion('+topicId+')">··· voir les '+(nbrOtherMessages-1)+' autres réponses ···</a>' : '';
    
    return '<div class="topic '+topicType+'">\
                <h3 onclick="toggle(\'replies'+topicId+'\')">\
                    <span style="font-weight:normal">&#x1F4AC;</span> '+topicTitle+'\
                </h3>\
                <div id="replies'+topicId+'">\
                    '+htmlDiscussionMessage(firstMessage.message, firstMessage.is_json, firstMessage.author_pseudo, firstMessage.datetime_utc, nbrOtherMessages+1)+'\
                    '+otherMessagesLink+'\
                    '+htmlDiscussionMessage(lastMessage.message, lastMessage.is_json, lastMessage.author_pseudo, lastMessage.datetime_utc, nbrOtherMessages+1)+'\
                </div>\
                <div class="reply_button">\
                    <a id="replyButton'+topicId+'" href="#" onclick="display(\'sendform'+topicId+'\');this.style.display=\'none\';return false">\
                        Commenter\
                    </a>\
                    <form id="sendform'+topicId+'" method="post" action="" onsubmit="replyDiscussion('+topicId+', '+(nbrOtherMessages+1)+'); return false;">\
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
        let apiDatas = JSON.parse(message).datas;
        formattedMessage = "&#x1F489; <strong>"+apiDatas.author.citizen_pseudo+"</strong> a soigné la blessure \
                         de <strong>"+apiDatas.target.citizen_pseudo+"</strong> en zone "+apiDatas.coord_x+":"+apiDatas.coord_y;       
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


function htmlLogEvents(apiData) {
    
    var coords = apiData.coord_x+":"+apiData.coord_y,
        dateString = dateIsoToString(apiData.datetime_utc),
        citizen_id = getCitizenId(),    
        iAmInvolved = (citizen_id === apiData.author.citizen_id || citizen_id === apiData.target.citizen_id) ? true : false;
    
    if (apiData.event_type === "heal_citizen") {
        return htmlEvent("&#x1F489; <strong>"+apiData.author.citizen_pseudo+"</strong> a soigné la blessure \n\
                         de <strong>"+apiData.target.citizen_pseudo+"</strong>", 
                         "en zone "+coords, dateString, iAmInvolved);
    }
    else if (apiData.event_type === "attack_citizen") {
        return htmlEvent("&#x1F44A;&#x1F3FC; <strong>"+apiData.author.citizen_pseudo+"</strong> \n\
                         a agressé <strong>"+apiData.target.citizen_pseudo+"</strong> !", 
                         "en zone "+coords, dateString, iAmInvolved);
    }
    else {
        return htmlEvent("<strong class=\"red\">[BUG] Evénement non prévu - Signalez-le au développeur du jeu...</strong>", 
                         "", dateString, iAmInvolved);
    }
}


function htmlNewDiscussionForm(citizenPseudo)
{

    var fieldPseudoStyle = (citizenPseudo === "") ? '' : 'display:none';

    return '<div id="newDiscussion"></div>\
        <div id="send" class="topic discuss">\
            <div class="message">\
                <a href="#" id="buttonNewTopic">\
                    &#x270F;&#xFE0F; Ajouter un message...\
                </a>\
                <form id="sendform" style="display:none">\
                    <a href="#" id="hideSendform">[masquer]</a>\
                    <div>&#x1F464; <strong>'+citizenPseudo+'</strong>\
                        <div id="errorNewTopicPseudo" class="red"></div>\
                        <input id="guestPseudo" type="text" name="guest_pseudo" placeholder="Votre pseudo" style="'+fieldPseudoStyle+'">\
                    </div>\
                    <div id="errorNewTopicMessage" class="red"></div>\
                    <textarea id="messageNew" placeholder="Donnez votre avis sur les stratégies ou demandez de l\'aide..."></textarea>\
                    <input type="text" id="titleNew" placeholder="Titre de la discussion (facultatif)">\
                    <input type="submit" value="Envoyer">\
                </form>\
            </div>\
        </div>';
}
