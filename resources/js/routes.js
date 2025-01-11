import Vue from 'vue';
import Router from 'vue-router';
Vue.use(Router);
const routes = [
    {
        path: '*',
        name: 'not-found',
        component: require('./components/NotFound.vue').default
    },
    {
        path: '/Account',
        name: 'Account',
        component: require('./components/Account/Index.vue').default,
    },
    {
        path: '/Products',
        name: 'Products',
        component: require('./components/Products/Index.vue').default,
    },
    {
        path: '/Report',
        name: 'Report',
        component: require('./components/Report/Index.vue').default,
    },
    {
        path: '/FileReport',
        name: 'Create',
        component: require('./components/Report/Create.vue').default,
    },
    {
        path: '/Replenishment',
        name: 'Replenishment',
        component: require('./components/Replenishment/Index.vue').default,
    },
    {
        path: '/ProductsCreate',
        name: 'Create',
        component: require('./components/Products/Create.vue').default,
    },
    {
        path: '/Purchase',
        name: 'Purchase',
        component: require('./components/PurchaseOrder/Index.vue').default,
    },
    {
        path: '/Order',
        name: 'Order',
        component: require('./components/CompletedOrder/Index.vue').default,
    },
    {
        path: '/Category',
        name: 'Category',
        component: require('./components/Category/Index.vue').default,
    },
    {
        path: '/CategoryCreate',
        name: 'Create',
        component: require('./components/Category/Create.vue').default,
    },
    {
        path: '/Measurement',
        name: 'Measurement',
        component: require('./components/Measurement/Index.vue').default,
    },
    {
        path: '/MeasurementCreate',
        name: 'Create',
        component: require('./components/Measurement/Create.vue').default,
    },
    {
        path: '/Student',
        name: 'Student',
        component: require('./components/Student.vue').default,
    },
    {
        path: '/Courses',
        name: 'Courses',
        component: require('./components/Courses.vue').default
    },
    //---------------------------------------------------------USER
    {
        path: '/users',
        name: 'users',
        component: require('./components/Users/IndexUser.vue').default,
        props: true,
    },
    {
        path: '/users_seller',
        name: 'users_seller',
        component: require('./components/Users/UserSeller.vue').default,
        props: true,
    },
    {
        path: '/users_buyer',
        name: 'users_buyer',
        component: require('./components/Users/UserBuyer.vue').default,
        props: true,
    },
    //---------------------------------------------------------END USER
    //---------------------------------------------------------USER
    {
        path: '/deliquency',
        name: 'deliquency',
        component: require('./components/Deliquency/IndexDeliquency.vue').default,
        props: true,
    },
    //---------------------------------------------------------END USER
    //---------------------------------------------------------ROLE
    {
        path: '/role',
        name: 'role',
        component: require('./components/Role/IndexRole.vue').default,
        props: true,
    },
    // {
    //     path: '/role-add/:page',
    //     name: 'role_add',
    //     component: require('./components/Role/AddRole.vue').default,
    //     props: true,
    // },
    //---------------------------------------------------------END ROLE
    //---------------------------------------------------------PERMISSION
    {
        path: '/permission',
        name: 'permission',
        component: require('./components/Permission/IndexPermission.vue').default
    },
    // {
    //     path: '/permission-add',
    //     name: 'permission-add',
    //     component: require('./components/Permission/AddPermission.vue').default
    // },
    //---------------------------------------------------------END PERMISSION
    //---------------------------------------------------------REGISTRAR
    {
        path: '/registrar',
        name: 'registrar',
        component: require('./components/Registrar/IndexRegistrar.vue').default
    },
    //---------------------------------------------------------END REGISTRAR
    //---------------------------------------------------------PROGRAM
    {
        path: '/program',
        name: 'program',
        component: require('./components/Program/IndexProgram.vue').default
    },
    //---------------------------------------------------------END PROGRAM
]
// Vue.component(
//     'enrollment-status',
//     require('./components/EnrollmentStatus.vue').default
// );
const router = new Router({
    mode: 'history',
    routes: routes
});

export default router
