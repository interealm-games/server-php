# server-php
Server written in PHP to handle requests as defined by the Server repo

## Requirements

### OpenTask
[OpenTask](https://github.com/interealm-games/opentask/releases) needs to be available at the root. Executables are included in the `/opentask` folder. On Windows, a symlink can be made with `mklink opentask.exe "opentask\opentask.exe"`. Mac and Linux will need to place a copy of an executable in a PATH accessible location.

After this is installed, you can check necessary programs with:
	
```
opentask requirements list
```

And check whether they are all installed (correctly) with:
	
```
opentask requirements test
```

## Initialize

Create an environment file from `/api/environments/.env.template` in the same folder.

REQUEST_HANDLERS_PATH points to the RequestHandler definitions (in another repository) which define the use of this server. Something like: `"../../editor-backend/public/index.php"`. You can include multiple paths, delimited by `:`. Preventing naming collisions of endpoints is up to the developer.

Run: 
```
opentask rungroup init
```

This should pull all Git submodules and install all PHP/Composer libraries.

You can update dependencies with: 
```
opentask rungroup update
```

## Build

```
opentask rungroup build
```
