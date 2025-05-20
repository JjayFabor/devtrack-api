# Authenticating requests

To authenticate requests, include a **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

    <b>Authentication Required:</b><br>
    <ul>
        <li>
            <b>Bearer Token</b>: First, log in to obtain your user token. Send it in the <code>Authorization</code> header as <code>Bearer &lt;token&gt;</code>.
        </li>
        <li>
            <b>API Key</b>: You must also generate an API key using the <b>API Key Management</b> endpoints. Provide your API key in the <code>X-API-KEY</code> header for all authenticated requests.
        </li>
    </ul>
    <b>Note:</b> Most endpoints require <u>both</u> the Bearer token and the API key in the headers.
