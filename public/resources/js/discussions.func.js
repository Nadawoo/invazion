/*
 * This script gathers all the functions related to the discussions space
 * (forum/communications).
 * Put only functions here, no immediatly executable code or events listeners.
 */


/**
 * Calls the API to get the list of the discussions, in the most performant way:
 * > By default, calls the API only once, then stores the result in memory (faster)
 * > If you need to update the results, you can force recalling the API (up-to-date but slower)
 * 
 * @param {string} refresh Set this value to "true" to force the function to call the API
 *                         even if the result of a previous call is already stored in memory.
 * @return jsonDiscussionApi JSON list of the discussions returned by the API
 */
async function callDiscussionApiOnce(topicType, refresh=false) {
    
    if (_jsonDiscussionApi === null || refresh === true) {        
        _jsonDiscussionApi = await callApi("GET", "discuss/threads", "action=get&sort=last_message_date&fullmsg=1&type="+topicType);
    }
    return _jsonDiscussionApi;
}


/**
 * Show/hide the vertical panel for the discussions and events
 */
async function enlargeWall() {
    
    let minBarHeight = "2.5rem",
        maxBarHeight = "100%";

    if (document.querySelector("#floating_wall").style.height !== maxBarHeight) {
        // Enlarges the panel
        document.querySelector("#floating_wall").style.height = maxBarHeight;
        document.querySelector("#wallHeader .arrow").style.transform = "rotate(+180deg)";
        document.querySelector("#floating_wall").style.zIndex = 60;
    }
    else {
        // Reduces the panel
        document.querySelector("#floating_wall").style.height = minBarHeight;
        document.querySelector("#wallHeader .arrow").style.transform = "rotate(0)";
        document.querySelector("#floating_wall").style.zIndex = 0;
    }
    
    // Loads the discussions tab by default
    initiateDiscussTab();
}


/**
 * Gets the discussions and write them in the "discussions" tab.
 * Note that this function doesn't display the tab: this task is handled by Materialize.css
 */
async function initiateDiscussTab() {
    updateDiscussionsList("all");
    // Add the listener on the form to create a topic.
    // TODO: make a cleaner code with DOMContentLoaded
    setTimeout(listenToSendform, 500);
}


/**
 * Gets the all the messages of a discussion by calling the InvaZion's API to 
 * 
 * @param {int} topicId the ID of the discussion to load
 * @returns {string} The JSON returned by the API
 */
async function loadDiscussion(topicId) {
    
    var json = await callApi("GET", "discuss/threads", `action=get&topic_id=${topicId}`),
        messages = json["datas"]["messages"],
        i = 0;
    
    let thread = document.querySelector(`#topic${topicId} .replies`);
    // Remove the preview of the first & last message of the topic
    thread.innerHTML = "";
    // Insert all the messages of the topic
    for(let msg in messages) {
        i++;
        htmlMessage = htmlDiscussionMessage(messages[msg]["message"], messages[msg]["is_json"], 
                                            messages[msg]["author_pseudo"], messages[msg]["datetime_utc"], i);
        thread.appendChild(htmlMessage);
    }
}


/**
 * To start a new discussion
 * @returns {Boolean}
 */
async function createDiscussion() {
    
    let title         = document.getElementById("titleNew").value,
        message       = document.getElementById("messageNew").value,
        guest_pseudo  = document.getElementById("guestPseudo").value,
        author_pseudo = document.getElementById("citizenPseudo").innerHTML,
        token         = getCookie('token');

    let json = await callApi("POST", "discuss/threads", `action=create&title=${title}&message=${message}&guest_pseudo=${guest_pseudo}&token=${token}`);
    let topicId = json.datas.topic_id;
    
    if (json.metas.error_code === "success") {
        // Display the new discussion thread
        document.getElementById("wallDiscuss").innerHTML += htmlDiscussion(topicId, "discuss", 0);
        document.querySelector(`#topic${topicId} .title`).textContent = title;
        document.querySelector(`#topic${topicId} .replies`).appendChild( htmlDiscussionMessage(message, 0, author_pseudo, Date(), 1) );
        // Hide the form to create a new thread
        toggleSendform(null);
        // Clear the form for the eventual next thread to send
        document.getElementById("sendform").reset();
    }
    else if (json.metas.error_code === "undefined_pseudo") {
        document.getElementById("errorNewTopicPseudo").innerHTML = json.metas.error_message;
    }
    else {
        document.getElementById("errorNewTopicMessage").innerHTML = json.metas.error_message;
    }
}


function displayReplyForm(topicId) {
    
    let thread = document.querySelector(`#topic${topicId}`);
    thread.querySelector('.replyButton').style.display = 'none';
    thread.querySelector('.sendform').style.display = 'block';
    thread.querySelector('textarea').focus();
}


/**
 * To send a reply in an existing discussion
 */
async function replyDiscussion(topicId, nbrMessages) {
    
    let citizenPseudo = document.getElementById("citizenPseudo").innerHTML,
        message  = document.querySelector(`#topic${topicId} textarea`).value,
        token    = getCookie('token');
        
    let json = await callApi("POST", "discuss/threads", `action=reply&topic_id=${topicId}&message=${message}&token=${token}`);
    
    if (json.metas.error_code === "success") {
        let thread = document.querySelector(`#topic${topicId}`);
        // Clears and hides the form after posting
        thread.querySelector('textarea').value = "";
        thread.querySelector('.sendform').style.display = "none";
        // Show the "Reply" button again
        thread.querySelector('.replyButton').style.display = "block";
        // Appends the text of the posted reply at the bottom of the discussion
        thread.querySelector('.replies').appendChild( htmlDiscussionMessage(message, false, citizenPseudo, new Date().toISOString(), nbrMessages+1) );
        // Clears the eventual error message (obsolete after sending)
        thread.querySelector('.replyError').innerHTML = "";
    }
    else {
        thread.querySelector('.replyError').innerHTML = '<span class="red">'+json.metas.error_message+'</span>';
    }
}


/**
 * Show/hide the form to create a new discussion thread
 */
function toggleSendform(event) {
    
    toggle("send");
    toggle("sendform");
    toggle("buttonNewTopic");
    
    // When we hide the form, event = null
    if(event !== null) {
        // Desactivate the normal form
        event.preventDefault();
        // Put the cursor in the text area to allow direct typing
        document.querySelector("#messageNew").focus();
        document.getElementById("wallDiscuss").scrollIntoView(false);
    }
}


/**
 * Builds the url to a discussion, eventually with an anchor to a reply
 * Example : https://invazion.nadazone.fr/discuss/topic?topic=7&p=#msg37
 * @param {int} discussionId The ID of the discussion
 * @param {int} messageId    The ID of a message inside the discussion, if you want to
 *                            direct the user directly on it.
 * @return {String}
 */
function urlDiscussion(discussionId, messageId="") {
    
    return getOfficialServerRoot()+'/discuss/topic?topic='+discussionId+'#msg'+messageId;
}


/**
 * Displays the discussions on the constructions page in the city.
 * 
 * @param {string} topicType Set to "event" to display only the game events
 *                           Set to "discuss" to display only the threads written by players
 *                           Any other value will display everything (events + discussions)
 */
async function updateDiscussionsList(topicType) {
    
    // Gets the titles of the discussions, by calling the InvaZion's API
    var jsonTopics = await callDiscussionApiOnce(topicType, refresh=true);
    
    var citizenPseudo = document.getElementById("citizenPseudo").innerHTML;
    var length = jsonTopics.datas.length;
    
    // Set in which wall to add the contents
    // TODO: this doesn't handle a wall to display all the contents is one global tab 
    // (discussions + events + attacks)
    if(topicType === "event") {
        var contentsId = "#wallEvents";
    } else if(topicType === "discuss") {
        var contentsId = "#wallDiscuss";
    }  else if(topicType === "all") {
        var contentsId = "#wallDiscuss";
    }
    
    // Important: avoids adding the same threads multiple times after reloading
    document.querySelector(contentsId).innerHTML = "";
    
    for (let i=0; i<length; i++) {        
        let topic            = jsonTopics.datas[i],
            topicId          = topic.topic_id,
            nbrReplies       = topic.nbr_messages-1;
        
        document.querySelector(contentsId).innerHTML += htmlDiscussion(topicId, topic.topic_type, nbrReplies);
        document.querySelector(`#topic${topicId} .title`).innerHTML = topic.title;
        
        let replies = document.querySelector(`#topic${topicId} .replies`);
        // Preview of the first message of the topic
        replies.prepend( htmlDiscussionMessage(topic.first_message.message, 
                                               topic.first_message.is_json,
                                               topic.first_message.author_pseudo,
                                               topic.first_message.datetime_utc,
                                               1) );
        // If there is no reply, the last message is the same as the first one, 
        // so don't display it two times.
        if(nbrReplies > 0) {
            replies.appendChild(  htmlDiscussionMessage(topic.last_message.message,
                                                        topic.last_message.is_json,
                                                        topic.last_message.author_pseudo,
                                                        topic.last_message.datetime_utc,
                                                        topic.nbr_messages) );
        }
    }
    
    document.querySelector("#wall .footer").innerHTML = htmlNewDiscussionForm(citizenPseudo);
    document.getElementById("wallDiscuss").scrollIntoView(false);
}
