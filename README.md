# Litecoin Node Interface

This is a basic web interface to a Litecoin full node. Currently displays info about the node (version, subversion), the blockchain (blocks and headers, difficulty, median time), network usage, and a list of connected/banned nodes. You can see how it looks [here](http://ltc.xblau.com).

If you have any suggestion or problems, feel free to open an issue or pull request.

### Requirements

1. Litecoin full node with username/password authentication and RPC enabled.
2. Web server with PHP(5) and the curl extension.

### Installing

1. Clone the repo or extract the files on any public folder.
2. Copy `config-example.php` to `config.php` and edit it:

`serverurl` is the IP address of the Litecoin full node and the RPC port. If you are running it on the same machine as your web server and using the default port, you don't need to change this.

`username` and `password` are your RPC credentials (as specified on your `litecoin.conf` file, for example).

3. **Optionally:** if your web server is publicly accessible, you may want to configure HTTP authentication. Also, as a precaution, please don't use this on a node with a wallet.

### License

This project is free software, licensed under the terms of the MIT License. See the `LICENSE` file on this repository or [opensource.org/licenses/mit](https://opensource.org/licenses/mit).

If you found this useful, small donations are welcome! `LZgV7GwPYQtCpeXuR4crGZTRzmSEEMKQQH`
