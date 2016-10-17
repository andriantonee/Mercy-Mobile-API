# Installation

####1. Install Dependency

```sh
$ composer install
```

####2. Set Database Table

- Make Sure Table [oauth_refresh_tokens] Column [access_token_id] has been set foreign key to Table [oauth_access_tokens] Column [id] ON UPDATE CASCADE ON DELETE CASCADE