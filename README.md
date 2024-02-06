Symfony OIDC Demo
=================

A minimal example of using
[OIDC with Symfony](https://symfony.com/doc/current/security/access_token.html#using-openid-connect-oidc).

Set-up
------

Configure Keycloak:

1. Start the compose project: `docker compose up -d` This boots up a local
   Keycloak instance;
2. Go to http://localhost:8080/admin/master/console and login using `admin:admin`;
3. Navigate to Realm settings and under "Tokens", set the "Default Signature
   Algorithm" to "ES256";
3. Navigate to Clients and create a new client. Enable the "Client authentication"
   switch;
4. In the client under "Credentials", copy the "Client secret";

Configure Symfony:

1. Define the Client ID and Secret from Keycloak in the `OIDC_CLIENT_ID`
   and `OIDC_CLIENT_SECRET` env vars;
2. Visit http://localhost:8080/realms/master/protocol/openid-connect/certs
   (replace `master` with the Realm ID if you created a new Realm in
   Keycloak);
3. Select the item with `alg: "ES256"` and copy the object as JSON
   (`{"kid":"...","kty":"EC","alg":"ES256",...}`). Store this as text in the
   `OIDC_JWK` env var.

Start the Symfony app:

1. Run `symfony serve -d` and navigate to https://localhost:8000 and follow
   the instructions on the page.
