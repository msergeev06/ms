var MS = MS || {};

MS.core = {};

MS.core.user = {};

MS.core.user.ID = 0;

MS.core.user.getID = function () {
    return MS.core.user.ID;
};

MS.core.user.bAdmin = false;

MS.core.user.isAdmin = function () {
    return MS.core.user.isAdmin();
};

MS.core.session = {};

MS.core.session.ID = "";

MS.core.session.getID = function () {
    return MS.core.session.ID;
};

function ms_sessid ()
{
    return MS.core.session.getID();
}
