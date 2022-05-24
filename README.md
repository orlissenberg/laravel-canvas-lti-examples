
# Laravel Canvas LTI (v2) Tool Example 

Register the tool with:

    https://canvas-app.test/lti/register

[LTI Implementation Guide - Version 2.0 Final Specification](https://www.imsglobal.org/specs/ltiv2p0/implementation-guide)

[Canvas LMS - REST API and Extensions Documentation](https://canvas.instructure.com/doc/api/file.graphql.html)

[Canvas - Quickstart](https://github.com/instructure/canvas-lms/wiki/Quick-Start)

[LTI-Tool-Provider-Library-PHP](https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP)

[OAuth 1 Client Library](https://github.com/thephpleague/oauth1-client)

[Canvas LMS Docker Image](https://hub.docker.com/r/orlissenberg/canvas-lms)

[YouTube - LTI B to C: Part 5 - LTI 2.0 Registration](https://www.youtube.com/watch?v=kisrxs2V3oc)

## Notes for running from source

Start Redis!

For macOS:

    brew install ruby@2.7
    gem install bundler:2.2.33
    bundle _2.2.33_ install

.zshrc additions:

    export PATH="/usr/local/Cellar/ruby@2.7/2.7.6/bin:$PATH"
    export LDFLAGS="-L/usr/local/Cellar/ruby@2.7/2.7.6/lib"
    export CPPFLAGS="-I/usr/local/Cellar/ruby@2.7/2.7.6/include"
    export PKG_CONFIG_PATH="/usr/local/Cellar/ruby@2.7/2.7.6/lib/pkgconfig"

## Additional References

[Laravel Socialite](https://laravel.com/docs/master/socialite)

[Socialite Providers](https://socialiteproviders.com)

[OAuth 2.0](https://oauth.net/2/)

[JSON Web Key Sets](https://auth0.com/docs/secure/tokens/json-web-tokens/json-web-key-sets)

[Canvas LMS API Documentation](https://canvas.instructure.com/doc/api/index.html)

[Canvas LTI Variable Substitutions](https://canvas.instructure.com/doc/api/file.tools_variable_substitutions.html)

[XML Config Builder](https://www.edu-apps.org/build_xml.html)

[LTI Version 1.3 Custom Parameter Substitution](https://www.imsglobal.org/spec/lti/v1p3/#customproperty)

[Canvas API Postman](https://gitlab.unimelb.edu.au/dsweeney/canvas-api-postman)

[An Illustrated Guide to OAuth and OpenID Connect](https://www.youtube.com/watch?v=t18YB3xDfXI&t=659s)
