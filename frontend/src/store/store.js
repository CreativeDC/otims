import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        userObj: null,
        userNodeObj: { title: "Unknown" },
        device: { desktop: false },
        receivingPackageObj: null,
        srBeneficiaryObj: null,
        thirdLanguagesObj: null,

        forwardRequestObj: null,
    },

    mutations: {
        setUser(state, userObj) {
            state.userObj = userObj;
        },

        setNode(state, userNodeObj) {
            state.userNodeObj = userNodeObj;
        },

        setReceivingPackage(state, receivingPackageObj) {
            state.receivingPackageObj = receivingPackageObj;
        },

        setSRBeneficiaryObj(state, srBeneficiaryObj) {
            state.srBeneficiaryObj = srBeneficiaryObj;
        },

        setThirdLanguagesObj(state, thirdLanguagesObj) {
            state.thirdLanguagesObj = thirdLanguagesObj;
        },

        setForwardRequest(state, forwardRequestObj) {
            state.forwardRequestObj = forwardRequestObj;
        }
    },
    getters: {
        userObj: state => state.userObj,
        userNodeObj: state => state.userNodeObj,
        device: state => state.device,
        receivingPackageObj: state => state.receivingPackageObj,
        srBeneficiaryObj: state => state.srBeneficiaryObj,
        thirdLanguagesObj: state => state.thirdLanguagesObj,
        forwardRequestObj: state => state.forwardRequestObj,
    }
});