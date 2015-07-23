            function converseAutoJoinChatPlugin( converse ) {
                var jids = [ 'bar@chat.consortium-horizon.com' ];
                converse.on('ready', function() {
                    var _transform = function (jid) {
                        var chatbox = converse.chatboxes.get(jid);
                        if (!chatbox) {
                            var roster_item = converse.roster.get(jid);
                            if (roster_item === undefined) {
                                // Assume MUC
                                converse.chatboxes.create({
                                    'id': jid,
                                    'jid': jid,
                                    'name': Strophe.unescapeNode(Strophe.getNodeFromJid(jid)),
                                    'nick': Strophe.unescapeNode(Strophe.getNodeFromJid(converse.jid)),
                                    'chatroom': true,
                                    'box_id' : b64_sha1(jid)
                                });
                            }
                            converse.chatboxes.create({
                                'id': jid,
                                'jid': jid,
                                'fullname': _.isEmpty(roster_item.get('fullname'))? jid: roster_item.get('fullname'),
                                'image_type': roster_item.get('image_type'),
                                'image': roster_item.get('image'),
                                'url': roster_item.get('url')
                            });
                        }
                    };
                    if (typeof jids === "string") {
                        _transform(jids);
                    }
                    _.map(jids, _transform);
                });
            };
            converse.plugins.add('converseAutoJoinChatPlugin', converseAutoJoinChatPlugin);