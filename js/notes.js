function selectNote(noteId)
{
	if (!noteChanged || confirm("Are you sure you want to discard your changes?")) {
		// get the handle of the clicked element 
        	var selectedNote = $("[id='" + noteId + "']"); 
        	var note_name = selectedNote.attr("title");
        	// undo the old selected note (if there is one)
        	var oldSelected = $("li.notename-selected");
        	if (oldSelected) {
        		oldSelected.attr("class","notename");
        		//oldSelected.children("a").css("font-weight", "normal");
        	}
        	// bold the selected notename
        	//selectedNote.children("a").css("font-weight", "bold");
        	// set its class to 'selected'
        	selectedNote.attr("class","notename-selected");
	      	// set note title field
	        $("#note-title").val(note_name);
	        $("textarea#note-area").focus();
        	$("textarea#note-area").val("Loading content..."); 
        	$.get("get_note.php",{note:noteId},function (note_contents) {
        			// check if the request failed
        			if (!note_contents || note_contents == "false") {
        				alert("Failed to retrieve note.");
        			} else {
        				//add the note to the text area
           				$("textarea#note-area").val(note_contents);    
           				noteChanged = false;   	
           				$("#save-button").attr("disabled",true);	 
           			} 
        		}       
    		);
    	}
}

function clearSelection()
{
	var oldSelected = $("li.notename-selected");
        if (oldSelected) oldSelected.attr("class","notename");
        $("#note-title").val("");    
        $("textarea#note-area").val("Type your note here.");
        noteChanged = false;
}

function saveSelection()
{
	var selected = $("li.notename-selected");
        var newTitle = $("input#note-title").val();
      	var noteData = $("textarea#note-area").val();
       	if (selected.length == 0) {
       		
       		// create a new note
	 		$.post("new-note.php", {note:newTitle,entry:noteData}, function (response) {
	   				if (response > 0) {
	   					$("li.notename:last").after('<li class="notename" id="' + response + '" title="' + newTitle + '"><a href="#">' + newTitle + '</a></li>');
	   					selectNote(newTitle);
	   				} else alert("Failed to create.");
	   			}       
 		);
        } else {
       		var noteId = selected.attr("id");
       		var noteName = selected.attr("title");
       		$.post("save_note.php", {id: noteId, noteName: noteName, newTitle:newTitle, entry:noteData}, function (response) {
       				if (response == '1') {
       					selected.children("a").text(newTitle);
       					selected.attr("title",newTitle);
       					noteChanged = false;
       					$("#save-button").attr("disabled",true);
       					alert("Saved.");
       				} else alert("Failed to save.");
       			}       
		);
	}
}

function handleNoteChanged() {
	$("#save-button").attr("disabled",false);
	noteChanged = true;
}

var noteChanged = false;

$(document).ready(function() {
	
	$( "#note-names" ).sortable({ 
	
		items: "> li:not(:last)",
	
		update : function () {

            var neworder = new Array();

            $("#note-names li:not(:last)").each(function(index) {    

                //get the id
                var id  = $(this).attr("id");
                var item = new Object(); // or var map = {};
                item["id"] = id;
                item["location"] = index;
                //push the object into the array
                neworder.push(item);

            });

            $.post("update_note_order.php",{'neworder': neworder},function(response){});

        }
	
	});
    $( "#note-names").disableSelection( { items: "> li:not(:last)" } );
	
	// Opening notes
	$('#note-names').on('click','.notename a', function(){
		// get the handle of the clicked element 
	        var selectedNote = $(this).parent(); 
	        var id = selectedNote.attr("id");
	        selectNote(id);
	});
	
	// Saving notes
	$('#save-button').on('click', function(){
		saveSelection();
	});

    // New note
	$('#new-note').on('click', function(){
		var new_note_index = 1;
		var new_note_name = "New Note";
		while ($("#note-names li[title='" + new_note_name + "']").length > 0) {
			new_note_index++;
			new_note_name = "New Note (" + new_note_index + ")";
		}
		var defaultNote = "Type your note here.";
    	$.post("new_note.php", {note:new_note_name,entry:defaultNote}, function (response) {
    			if (response > 0) {
    				$("#note-names li:last").before('<li class="notename" id="' + response + '" title="' + new_note_name + '"><a href="#">' + new_note_name + '</a></li>');
    				selectNote(response);
    			} else alert("Failed to create.");
    		}       
		);
        });
    
	// Deleting notes
	$('#delete-button').on('click', function(){
                var selected = $("li.notename-selected");
                if (selected.length == 0) {
                	alert("No note selected.");
                } else {
        		var noteId = selected.attr("id");
        		var noteName = selected.attr("title");
        		var response = confirm("Are you sure you want to delete note '" + noteName + "'?");
			if (response == true) {
        			$.post("delete_note.php", {note:noteId}, function (response) {
        					if (response == '1') {
        						//var index = $('#note-names li').index($('.notename-selected'));
        						selected.remove();
        						//var newSelected = $("#note-names li:nth-child(" + index + ")").attr("id");
        						//selectNote(newSelected);
        						clearSelection();
        					} else alert("Failed to delete.");
        				}       
    				);
    			}
    		}
        });
        
	// catching tabs in the text area
    $("#note-area").keydown(function(e) {
            if (e.keyCode === 9) { // tab was pressed
	        // get caret position/selection
	        var start = this.selectionStart;
	            end = this.selectionEnd;
	
	        var $this = $(this);
	
	        // set textarea value to: text before caret + tab + text after caret
	        $this.val($this.val().substring(0, start)
	                    + "\t"
	                    + $this.val().substring(end));
	
	        // put caret at right position again
	        this.selectionStart = this.selectionEnd = start + 1;
	
	        // prevent the focus lose
	        return false;
	    } else if (!( String.fromCharCode(e.which).toLowerCase() == 's' && e.ctrlKey) && !(e.which == 19)) {
	        return true;
            } else {
            	saveSelection();
	    	event.preventDefault();
                return false;
	    } 
	});
	
	$("#note-area").keyup(function(e) {
            if ($(this).val() != $(this).data('initial_value')) {
               handleNoteChanged();
            }
	});
	
	$("#note-title").keyup(function(e) {
            if ($(this).val() != $(this).data('initial_value')) {
               handleNoteChanged();
            }
	});
	
	$("#note-area").data('initial-value', $(this).val());
	
	$("#note-title").data('initial-value', $(this).val());
});