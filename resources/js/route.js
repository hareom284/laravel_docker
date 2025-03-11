const pages = {


    // admin dashboard
    "Company/System/Presentation/Resources/index": import('../../src/Company/System/Presentation/Resources/index.vue'),

    //organization
    "Company/Organization/Presentation/Resources/Organization/Index": import('../../src/Company/Organization/Presentation/Resources/Organization/Index.vue'),

    // plans
    "Company/Organization/Presentation/Resources/Plans/Index": import('../../src/Company/Organization/Presentation/Resources/Plans/Index.vue'),




    //user module
    "Company/Security/Presentation/Resources/Users/Index": import('../../src/Company/Security/Presentation/Resources/Users/Index.vue'),




    // Security Domain
    "Company/Security/Presentation/Resources/Permissions/Index": import('../../src/Company/Security/Presentation/Resources/Permissions/Index.vue'),
    "Company/Security/Presentation/Resources/Roles/Index": import('../../src/Company/Security/Presentation/Resources/Roles/Index.vue'),

    //authnication route :🧑
    "Auth/Presentation/Resources/Login": import("../../src/Auth/Presentation/Resources/Login.vue"),
    "Auth/Presentation/Resources/Register": import("../../src/Auth/Presentation/Resources/Register.vue"),
    "Auth/Presentation/Resources/Verify": import("../../src/Auth/Presentation/Resources/Verify.vue"),
    "Auth/Presentation/Resources/UserProfile": import('../../src/Auth/Presentation/Resources/UserProfile.vue'),


    //announcement
    "Company/System/Presentation/Resources/Announcements/Index": import('../../src/Company/System/Presentation/Resources/Announcements/Index.vue'),

    //settings
    "Company/System/Presentation/Resources/Settings/Index": import('../../src/Company/System/Presentation/Resources/Settings/Index.vue'),


};
export default pages;
