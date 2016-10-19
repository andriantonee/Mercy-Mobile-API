# Installation

####1. Make Sure You Have Create Database **_tournamensia_**.

####2. Install Dependency

```sh
$ composer install
```

####3. Set Database Table

- Make Sure Table **_oauth_refresh_tokens_** Column **_access_token_id_** has been set foreign key to Table **_oauth_access_tokens_** Column **_id_** ON UPDATE CASCADE ON DELETE CASCADE.
- Make Sure if you are using **Postman**, **Advanced REST client**, etc ensure the data is encoded before sending to server.
- Make Sure you change the Table **_oauth_access_tokens_** Column **_user_id_**, Table **_oauth_clients_** Column **_user_id_** dan Table **_oauth_auth_codes_** Column **_user_id_** type data to varchar (20).