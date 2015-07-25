
jQuery(document).ready(function(){
	// Provide indexOf function for IE8
	if(!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(what, i) {
      i = i || 0;
      var L = this.length;
      while (i < L) {
        if(this[i] === what) return i;
					++i;
      }
      return -1;
    };
	}

	// Show all the parts to be used only with JS enabled
	$('.AssignAward .Fields .WithScript').removeClass('Hidden');
	// Hide all the parts to be used only with JS disabled
	$('.AssignAward .Fields .NoScript').addClass('Hidden');

	// @var array Stores the list of selected User IDs.
	var SelectedUserIDs = [];

	// @var int The ID of the Award being assigned.
	var AwardID = gdn.definition('AwardID');

	// Prepare the Icon to be used to remove a Selected User from the list
	var BaseRemoveIcon = $('<span>')
		.addClass('Remove Icon')
		.attr('title', gdn.definition('Remove_User'))
		.text('X');

	/**
	 * Callback function. It runs when the User selects an entry from the option
	 * list.
	 *
	 * @param object Item The selected item.
	 */
	function SelectUser(Item) {
		if(!Item) {
			return;
		}

		// Retrieve User Object
		var User = Item.user;

		// If User was already selected, skip it
		if(SelectedUserIDs[User.UserID]) {
			return;
		}
		// Store the User ID amongst the selected ones
		SelectedUserIDs.push(User.UserID);

		// Build a link to User's profile
		var UserProfileLink = $('<a>')
			.attr('href', gdn.definition('WebRoot') + '/profile/' + User.UserID + '/' + User.UserName)
			.text(gdn.definition('View_Profile'));
		// Build a label for the User
		var UserLabel = $('<span>')
			.html(User.UserName + ' &lt;' + User.EmailAddress + '&gt;');

		// Prepare the Remove Icon for the element
		var RemoveIcon = BaseRemoveIcon
			.clone()
			.attr('id', 'RemoveUser_' + User.UserID)
			.attr('title', gdn.definition('Remove_User'));

		// Add User to the list of the selected ones
		var UserElement = $('<div>')
			.attr('id', User.UserID)
			.addClass('SelectedUser')
			.html(RemoveIcon)
			.append(UserLabel)
			.append(UserProfileLink)
			.prependTo("#SelectedUsers");

		// If User already got the Award, format it accordingly
		if(User.DateAwarded) {
			UserElement.addClass('EarnedAward');
			UserLabel.attr('title', User.AwardedMsg)
		}

		$("#SelectedUsers").scrollTop(0);
	}

	/**
	 * Removes a User ID from the list of the selected ones.
	 *
	 * @param int UserID The ID of the User.
	 */
	function RemoveUser(UserID) {
		// Remove the element from the internal array
		var ElementIdx = SelectedUserIDs.indexOf(UserID);
		if(ElementIdx >= 0) {
			SelectedUserIDs.splice(ElementIdx, 1);
		}
		// Remove the element from the list
		$('#' + UserID).remove();
	}

	// Load the Autocomplete for the User Search Box
	var UserSearchBox = $('#Form_UserName').autocomplete({
		source: function(request, response) {
			minLength: 2,
			$.ajax({
				url: gdn.definition('WebRoot') + '/user/searchwithaward/' + request.term + '/' + AwardID,
				dataType: 'json',
				data: {
				},
				success: function(data) {
					response($.map(data.Users, function(item) {
						// Set the "User got the Award" message
						item.AwardedMsg = item.DateAwarded ? gdn.definition('User_Received_Award') + item.DateAwarded : '';

						return {
							// Store the full User Object for later use
							user: item,
							label: item.UserName + ' <' + item.EmailAddress + '> ' + item.AwardedMsg,
							value: item.UserID
						}
					}));
				}
			});
		},
		select: function(event, ui) {
			if(ui.item) {
				SelectUser(ui.item);
			}
			return false;
		},
		open: function() {
			$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
		},
		close: function() {
			$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
		}
	});

	// Bind the events to the buttons
	$('#Form_Awards')
		.delegate('#Form_OK', 'click', function() {
			$('#Form_UserIDList').val(SelectedUserIDs.join(','));
		})
		.delegate('.Remove.Icon', 'click', function(element) {
			// Extract the ID of the User to remove
			UserID = $(this).attr('id').split('_')[1];
			RemoveUser(UserID);
		});
});
