
<div id="help">
    <p>
    <h3>Profile Settings</h3>
    <hr />
    </p>
    <p>
    <b>
    Profile Name
    </b>
    <br />
    Indicates a logical, user readable, identity of the SIP settings.
    </p>
    <p>
    <b>
    App Reference
    </b>
    <br />
    This is the logical name that your settings will be known as. This is used to link your settings to other settings
    e.g. VoIP Settings.
    <br />
    The App Reference should be unique.
    </p>
    <p>
    <b>
    Service Provider
    </b>
    <br />
    SIP Profile Type. Currently only IETF is supported
    </p>

    <p>
    <b>
    Public Username
    </b>
    <br />
    Address-Of-Record/Public User Identity. This is a SIP or SIPS URI according to IETF RFC 3261.
    The Value MAY be given without sip: or sips: prefix.
    </p>

    <p>
    <b>
    Registration
    </b>
    <br />
    Indicates if automatic registration is on or off.
    </p>

    <p>
    <h3>Proxy Server Settings</h3>
    <hr />
    </p>

    <p>
    <b>
    Proxy Server Address
    </b>
    <br />
    Address of the outbound proxy/P-CSCF. Valid values are IPV4/6 address, domain or FQDN. Value MAY be given without sip:
    or sips: prefix.
    </p>

    <p>
    <b>
    Realm
    </b>
    <br />
    Realm of the outbound proxy/P-CSCF. Note that this parameter must be
    exactly the same text string that the proxy server returns in the realm parameter of the
    Proxy-Authenticate header of 407 response to REGISTER.
    </p>

    <p>
    <b>
    Username
    </b>
    <br />
    Username for outbound proxy/P-CSCF realm.
    </p>

    <p>
    <b>
    Password
    </b>
    <br />
    Password for outbound proxy/P-CSCF realm.
    </p>

    <p>
    <b>
    Transport Type
    </b>
    <br />
    Transport protocol to be used between terminal and outbound proxy/P-CSCF.
    If this parameter is set to auto, the transport protocol will be automatically selected.
    </p>

    <p>
    <b>
    Allow loose routing
    </b>
    <br />
    Indicates if loose routing is on or off. Loose routing is the "lr" parameter as defined in RFC 3261
    </p>

    <p>
    <b>
    Port
    </b>
    <br />
    Port number on outbound/P-CSCF proxy.
    </p>

    <p>
    <h3>Registrar Server Settings</h3>
    <hr />
    </p>

    <p>
    <b>
    Registrar Server Address
    </b>
    <br />
    Address of the registrar proxy/S-CSCF. Valid values are IPV4/6 address, domain or FQDN. Value MAY be given without sip:
    or sips: prefix.
    </p>

    <p>
    <b>
    Realm
    </b>
    <br />
    Realm of the registrar proxy/S-CSCF. Note that this parameter must be
    exactly the same text string that the proxy server returns in the realm parameter of the
    Proxy-Authenticate header of 407 response to REGISTER.
    </p>

    <p>
    <b>
    Username
    </b>
    <br />
    Username for registrar proxy/S-CSCF
    </p>

    <p>
    <b>
    Password
    </b>
    <br />
    Password for registrar proxy/S-CSCF
    </p>

    <p>
    <b>
    Transport Type
    </b>
    <br />
    Transport protocol to be used between terminal and registrar proxy/S-CSCF.
    If this parameter is set to auto, the transport protocol will be automatically selected.
    </p>

    <p>
    <b>
    Port
    </b>
    <br />
    Port number on registrar proxy/S-CSCF
    </p>


</div>
