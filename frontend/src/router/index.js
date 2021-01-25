import Vue from 'vue';
import Router from 'vue-router';
import Home from '@/components/Home';
import SendPackage from '@/components/SendPackage';
import Login from '@/components/Login';

import ReceivePackage from '@/components/ReceivePackage';

import CurrentBalance from '@/components/CurrentBalance';

import StudentDist from '@/components/StudentDist';
import SendReceiveBeneficiary from '@/components/SendReceiveBeneficiary';

import AdminManagePackages from '@/components/AdminManagePackages';
import AdminManageNodes from '@/components/AdminManageNodes';
import AdminSyncSchools from '@/components/AdminSyncSchools';

import Reports from '@/components/Reports';

import SupplementaryReports from '@/components/SupplementaryReports';

import RequestBooks from '@/components/RequestBooks';
import SendRequest from '@/components/SendRequest';
import ForwardRequest from '@/components/ForwardRequest';

Vue.use(Router);

export const router = new Router({
  // mode: 'history',
  routes: [
    {
      path: '/',
      component: Home,
      name: 'Home',
    },
    {
      path: '/home',
      name: 'Home2',
      component: Home
    },
    {
      path: '/send-package',
      name: 'SendPackage',
      component: SendPackage
    },
    {
      path: '/receive-package',
      name: 'ReceivePackage',
      component: ReceivePackage
    },
    {
      path: '/request-books',
      name: 'RequestBooks',
      component: RequestBooks
    },

    {
      path: '/send-request',
      name: 'SendRequest',
      component: SendRequest
    },

    {
      path: '/forward-request',
      name: 'ForwardRequest',
      component: ForwardRequest
    },

    {
      path: '/current-balance',
      name: 'CurrentBalance',
      component: CurrentBalance
    },
    {
      path: '/student-dist',
      name: 'StudentDist',
      component: StudentDist
    },
    {
      path: '/sr-beneficiary',
      name: 'SendReceiveBeneficiary',
      component: SendReceiveBeneficiary
    },

    {
      path: '/reports',
      name: 'Reports',
      component: Reports
    },

    {
      path: '/srm',
      name: 'SupplementaryReports',
      component: SupplementaryReports
    },
    {
      path: '/admin/packages',
      name: 'AdminManagePackages',
      component: AdminManagePackages
    },
    {
      path: '/admin/nodes',
      name: 'AdminManageNodes',
      component: AdminManageNodes
    },
    {
      path: '/admin/sync',
      name: 'AdminSyncSchools',
      component: AdminSyncSchools
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
      meta: {
        plainLayout: true,
      },
    }
  ]
});