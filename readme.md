# A web server, written in vanilla PHP

Yes, it's kind of dumb, but it's for education.

TODO (in no particular order):

- open a socket on an IP + port (stream_socket_server stream_socket_accept)
- handle configuration file (via `ìni` file ?)
- handle configuration via cmd-line arguments
- parse an HTTP request
- serve static content
- serve dynamic PHP content
- provide parsed http request via PSR ServerRequest object
- handle sensible default value for response (we can do all the things)
- handle virtual hosts with traditionnal directives (host, port, root, index, "location") (configuration in JSON, or PHP class)
- handle HTTPS

