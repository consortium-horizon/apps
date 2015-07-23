    function converseAutoJoinChatPlugin( converse ) {
        var jids = [ 'bar@chat.consortium-horizon.com' ];
        converse.on('ready', function() {
            var _transform = function (jid) {
                var chatbox = converse.rooms.get(jid);
                if (!chatbox) {
                    converse.rooms.open(jid);
                }
            };
            if (typeof jids === "string") {
                _transform(jids);
            }
            _.map(jids, _transform);
        });
    };
    converse.plugins.add('converseAutoJoinChatPlugin', converseAutoJoinChatPlugin);