/*
 * This script gathers all the functions related to the discussions space
 * (forum/communications).
 * Put only functions here, no immediatly executable code or events listeners.
 */


/**
 * Highlights the active tab in the communication panel and inactivate the others
 * 
 * @param {type} activatedTab The tab to highlight
 * @return {undefined}
 */
function activateDiscussionTab(activatedTab) {
    
    var allTabs = document.getElementById("discussionTabs").children,
        allTabsIds = [];
    // Automatically list the tabs IDs
    for (let i = 0; i < allTabs.length; i++) {
        allTabsIds.push(allTabs[i].getAttribute("id"));
    }
    
    document.getElementById(activatedTab).className = "active_tab";
    
    for (let i = 0; i < allTabsIds.length; i++) {
        if (allTabsIds[i] !== activatedTab) {
            document.getElementById(allTabsIds[i]).className = "inactive_tab";
        }
    }
}


/**
 * Calls the API to get the list of the discussions, in the most performant way:
 * > By default, calls the API only once, then stores the result in memory (faster)
 * > If you need to update the results, you can force recalling the API (up-to-date but slower)
 * 
 * @param {string} refresh Set this value to "true" to force the function to call the API
 *                         even if the result of a previous call is already stored in memory.
 * @return jsonDiscussionApi JSON list of the discussions returned by the API
 */
async function callDiscussionApiOnce(refresh=false) {
    
    if (_jsonDiscussionApi === null || refresh === true) {        
        _jsonDiscussionApi = await callApi("GET", "discuss/threads", "action=get&sort=last_message_date&fullmsg=1");
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
        document.querySelector("#enlarge_wall .arrow").style.transform = "rotate(+180deg)";
    }
    else {
        // Reduces the panel
        document.querySelector("#floating_wall").style.height = minBarHeight;
        document.querySelector("#enlarge_wall .arrow").style.transform = "rotate(0)";
    }
    
    // Loads the discussions tab by default
    switchToDiscussTab();
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
        htmlMessages = "",
        i = 0;
    
    for(let msg in messages) {
        i++;
        htmlMessages += htmlDiscussionMessage(messages[msg]["message"], messages[msg]["is_json"], messages[msg]["author_pseudo"], messages[msg]["datetime_utc"], i);
    }
    document.getElementById("replies"+topicId).innerHTML = htmlMessages;
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
    
    if (json.metas.error_code === "success") {
        json.datas.message = message;
        json.datas.author_pseudo = author_pseudo;
        // Display the new discussion thread
        document.getElementById("newDiscussion").innerHTML += htmlDiscussion(json.datas.topic_id, title, json.datas, 0);
        hide("send");
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


/**
 * To send a reply in an existing discussion
 */
async function replyDiscussion(topicId, nbrMessages) {
    
    let citizenPseudo = document.getElementById("citizenPseudo").innerHTML,
        message  = document.getElementById("message"+topicId).value,
        token    = getCookie('token');
        
    let json = await callApi("POST", "discuss/threads", `action=reply&topic_id=${topicId}&message=${message}&token=${token}`);
    
    if (json.metas.error_code === "success") {
        // Clears and hides the form after posting
        document.getElementById("message"+topicId).value = "";
        hide("sendform"+topicId);
        // Unhides the "Reply" button
        display("replyButton"+topicId);
        // Appends the text of the posted reply at the bottom of the discussion
        document.getElementById("replies"+topicId).innerHTML += htmlDiscussionMessage(message, false, citizenPseudo, new Date().toISOString(), nbrMessages+1);
        // Clears the eventual error message (obsolete after sending)
        document.getElementById("replyError"+topicId).innerHTML = "";
    }
    else {
        document.getElementById("replyError"+topicId).innerHTML = '<span class="red">'+json.metas.error_message+'</span>';
    }
}


/**
 * Show/hide the form to create a new discussion thread
 */
function toggleSendform(event) {
    
    toggle("sendform");
    toggle("buttonNewTopic");
    // Desactivate the normal form
    event.preventDefault();
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
 */
async function updateDiscussionsList() {
    
    // Gets the titles of the discussions, by calling the InvaZion's API
    var jsonTopics = await callDiscussionApiOnce();
    
    var citizenPseudo = document.getElementById("citizenPseudo").innerHTML;
    var length = jsonTopics.datas.length;
    var discussions = "";
    
    for (let i=0; i<length; i++) {        
        let topic            = jsonTopics.datas[i],
            nbrOtherMessages = topic.nbr_messages-1;

        discussions += htmlDiscussion(topic.topic_id, topic.title, topic.last_message, nbrOtherMessages);
    }
    
    document.getElementById("wallDiscuss").innerHTML = htmlNewDiscussionForm(citizenPseudo)
                                                       + discussions;
}


/**
 * In the "communications" panel, activates the "Discussions" tab
 * @returns {undefined}
 */
function switchToDiscussTab() {
    
    display("wallDiscuss");
    hide(["wallNotifications", "wallEvents", "wallAttacks"]);
    activateDiscussionTab("tabWallDiscuss");
    updateDiscussionsList();
    // Add the listener on the form to create a topic.
    // TODO: make a cleaner code with async
    setTimeout(listenToSendform, 100);
}
