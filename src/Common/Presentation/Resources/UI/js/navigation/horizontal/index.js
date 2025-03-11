
const DASHBOARD_URL = "/home";
const DASHBOARD_ROUTE =  "dashboard";


export default [
    {
        title: 'Dashboard',
        url: '#',
        icon: { icon: 'mdi-home' },
        access_module: "access_dashboard",
        children: [
            { title: 'Home', url: DASHBOARD_URL, icon: { icon: 'mdi-home' }, route_name: DASHBOARD_ROUTE, },
        ],
    },
    {
        title: 'Projects',
        url: "#",
        icon: { icon: 'mdi-shopping' },
        route_name: 'organizations',
        access_module: "access_organization",
    },
    {
        title: 'Tasks',
        url: "#",
        icon: { icon: 'mdi-pencil-box-outline' },
        access_module: "access_user",
    },
    {
        title: 'Kanban',
        url: "/",
        icon: { icon: 'mdi-contact' },
        route_name: 'subscribers',
        access_module: "access_subscriber",
    },
    {
        title: 'Calendar',
        url: "/",
        icon: { icon: 'mdi-calendar-blank-outline' },
        route_name: 'libraries',
        access_module: "access_library",
    },
    {
        title: 'Contacts',
        url: "/",
        icon: { icon: 'mdi-contact' },
        route_name: 'system',
        access_module: "access_system",
    },
    {
        title: 'Messages',
        url: "/",
        icon: { icon: 'mdi-message-badge' },
        route_name: 'system',
        access_module: "access_system",
        // children: [
        //     { title: 'Website Manager', url: '/bc/admin', icon: { icon: 'mdi-alpha-r-circle' }, route_name: '/bc/admin', access_module: "access_pagebuilder", isNativeLink: true },
        //     { title: 'Setting Configuration', url: '/settings', icon: { icon: 'mdi-cog' }, route_name: '/settings', access_module: "access_pagebuilder" }

        // ]
    },
    {
        title: 'Products',
        url: "/",
        icon: { icon: 'mdi-pyramid' },
        route_name: 'system',
        access_module: "access_system",
    },
    {
        title: 'Invoices',
        url: "/",
        icon: { icon: 'mdi-content-paste' },
        route_name: 'system',
        access_module: "access_system",
    },
    {
        title: 'File Browser',
        url: "/",
        icon: { icon: 'mdi-file-cog' },
        route_name: 'system',
        access_module: "access_system",
    },
    {
        title: 'Notifications',
        url: "/",
        icon: { icon: 'mdi-bell-ring' },
        route_name: 'system',
        access_module: "access_system",
    },
    {
        title: 'Reports',
        url: "/",
        icon: { icon: 'mdi-format-float-left' },
        route_name: 'system',
        access_module: "access_system",
    },
    {
        title: 'Help Center',
        url: "/",
        icon: { icon: 'mdi-headset' },
        route_name: 'system',
        access_module: "access_system",
    },
]








