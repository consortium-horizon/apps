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


//OTher method

converse.plugins.add('myplugin', {

        overrides: {
            onConnected: function () {
                // Override the onConnected method in converse.js
                this._super.onConnected();
                var converse = this._super.converse;
                var jid = 'bar@chat.consortium-horizon.com';
                var chatbox = converse.rooms.get(jid);
                if (!chatbox) {
                    converse.rooms.open(jid);
                }


            },
        }
    });