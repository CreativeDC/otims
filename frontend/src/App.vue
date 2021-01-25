<template>
  <div id="app">
    <div v-if="!$route.meta.plainLayout">
      <div>
        <ul id="slide-out" class="sidenav">
          <li>
            <div class="user-view">
              <div class="background navbar-background"></div>

              <img class="circle" src="@/assets/img/person.jpg" />

              <span class="white-text name">{{ userObj.name }}</span>
              <span class="white-text email">{{ userObj.email }}</span>

              <span class="white-text email" style="font-weight: bold">{{
                userNodeObj.title
              }}</span>
            </div>
          </li>

          <li>
            <div class="form-field" style="padding: 5px; margin-top: -12px">
              <select
                id="localeSelect"
                v-model="$i18n.locale"
                @change="changeLocale(true)"
              >
                <option value="en">English</option>
                <option value="ps">پښتو</option>
                <option value="da">دری</option>
              </select>
            </div>
          </li>

          <li>
            <router-link
              class="waves-effect sidenav-close"
              id="homeBtn"
              to="/home"
            >
              <i class="material-icons left">home</i>
              {{ $t(`text.menu_dashboard`) }}
            </router-link>
          </li>
          <li>
            <router-link
              class="waves-effect sidenav-close"
              id="sendPackageBtn"
              to="/send-package"
            >
              <i class="material-icons left">file_upload</i>
              {{ $t(`text.menu_send_package`) }}
            </router-link>
          </li>
          <li>
            <router-link
              class="waves-effect sidenav-close"
              id="recvPackageBtn"
              to="/receive-package"
            >
              <i class="material-icons left">file_download</i>
              {{ $t(`text.menu_receive_package`) }}
            </router-link>
          </li>

          <li>
            <router-link
              class="waves-effect sidenav-close"
              id="reqBooksBtn"
              to="/request-books"
            >
              <i class="material-icons left">help_outline</i>
              Request Books
            </router-link>
          </li>

          <li>
            <router-link
              class="waves-effect sidenav-close"
              id="currentBalanceBtn"
              to="/current-balance"
            >
              <i class="material-icons left">view_list</i>
              {{ $t(`text.menu_current_inventory`) }}
            </router-link>
          </li>

          <li>
            <div class="divider"></div>
          </li>

          <li>
            <router-link
              class="waves-effect sidenav-close"
              id="studentDistBtn"
              to="/student-dist"
            >
              <i class="material-icons left">transfer_within_a_station</i>
              {{ $t(`text.menu_distribute_to_students`) }}
            </router-link>
          </li>

          <!-- <li>
            <router-link
              v-if="userObj.admin"
              class="waves-effect sidenav-close"
              id="srmBtn"
              to="/srm"
            >
              <i class="material-icons left">library_books</i>
              {{$t(`text.menu_srm_distribution`)}}
            </router-link>
          </li>-->

          <li v-if="userObj.admin">
            <ul>
              <li>
                <div class="divider"></div>
              </li>

              <li>
                <router-link
                  class="waves-effect sidenav-close"
                  id="reportsBtn"
                  to="/reports"
                >
                  <i class="material-icons left">data_usage</i>
                  {{ $t(`text.menu_distribution_reports`) }}
                </router-link>
              </li>

              <li>
                <router-link
                  class="waves-effect sidenav-close"
                  id="syncSchoolsBtn"
                  to="/admin/sync"
                >
                  <i class="material-icons left">sync</i>
                  Sync with EMIS
                </router-link>
              </li>

              <li>
                <a
                  class="waves-effect sidenav-close"
                  id="manageUsersBtn"
                  v-bind:href="lccUrl"
                  target="_blank"
                >
                  <i class="material-icons left">code</i>
                  {{ $t(`text.menu_manage_users`) }}
                </a>
              </li>

              <li>
                <router-link
                  class="waves-effect sidenav-close"
                  id="manageNodesBtn"
                  to="/admin/nodes"
                >
                  <i class="material-icons left">code</i>
                  {{ $t(`text.menu_manage_nodes`) }}
                </router-link>
              </li>

              <li>
                <router-link
                  class="waves-effect sidenav-close"
                  id="managePackagesBtn"
                  to="/admin/packages"
                >
                  <i class="material-icons left">code</i>
                  {{ $t(`text.menu_manage_packages`) }}
                </router-link>
              </li>
            </ul>
          </li>

          <li>
            <div class="divider"></div>
          </li>
          <li>
            <a
              class="waves-effect sidenav-close"
              href="#!"
              id="logoutBtn"
              v-on:click="logoutNow"
            >
              <i class="material-icons left">power_settings_new</i>
              {{ $t(`text.menu_logout`) }}
            </a>
          </li>
        </ul>

        <div id="header">
          <div class="row">
            <div class="col s2" style="margin-top: 20px">
              <a
                href="#"
                data-target="slide-out"
                class="sidenav-trigger"
                id="menuButton"
              >
                <i class="material-icons medium" style="color: white">menu</i>
              </a>
            </div>

            <div class="col s10">
              <div class="row right-align">
                <!-- <img src="img/moe_logo.png" style="height: 80px; width: 80px;"> -->

                <h3
                  id="otimsTitle"
                  class="right-align"
                  style="color: white; margin-right: 10px; margin-left: 10px"
                >
                  {{ $t(`text.otims_title`) }}
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- set progressbar -->
      <vue-progress-bar></vue-progress-bar>

      <div style="margin: 5px">
        <router-view />
      </div>
    </div>

    <div v-if="$route.meta.plainLayout">
      <!-- set progressbar -->
      <vue-progress-bar></vue-progress-bar>
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  name: "App",

  mounted() {},

  updated: function () {
    console.log("updated method called.");

    if (localStorage.locale) {
      this.$i18n.locale = localStorage.locale;
      this.setLocale();
    }
  },

  watch: {},

  data() {
    return {};
  },

  methods: {
    changeLocale: function (isReload) {
      localStorage.locale = this.$i18n.locale;

      if (isReload) {
        location.reload();
      }
    },

    setLocale: function () {
      var body = document.querySelector("body");
      var elem = document.querySelector(".sidenav");

      var instance = M.Sidenav.getInstance(elem);
      var snOptions = instance.options;

      instance.destroy();

      if (this.$i18n.locale == "en") {
        body.classList.remove("rtl");
        document.querySelector("html").dir = "ltr";

        snOptions.edge = "left";
        instance = M.Sidenav.init(elem, snOptions);

        document.getElementById("otimsTitle").classList.remove("left-align");
        document.getElementById("otimsTitle").classList.add("right-align");
      } else {
        body.classList.add("rtl");
        document.querySelector("html").dir = "rtl";

        document.querySelector("body").style.fontFamily = "Tahoma";

        snOptions.edge = "right";
        instance = M.Sidenav.init(elem, snOptions);

        document.getElementById("otimsTitle").classList.remove("right-align");
        document.getElementById("otimsTitle").classList.add("left-align");
      }
    },

    logoutNow: function () {
      console.log("Logging out");
      this.$store.commit("setUser", null);

      localStorage.email = "";
      localStorage.password = "";
      localStorage.remember = "";

      this.$router.push("/login");
    },
  },

  computed: {
    userObj: function () {
      return this.$store.getters.userObj;
    },

    userNodeObj: function () {
      return this.$store.getters.userNodeObj;
    },

    lccUrl: function () {
      var baseUrl = this.$http.defaults.baseURL;
      var lastSlashIndex = baseUrl.lastIndexOf("/");
      var url = baseUrl.substring(0, lastSlashIndex) + "/lccuser";
      return url;
    },
  },
};
</script>

<style>
.vpd-icon-btn,
.vpd-icon-btn > svg {
  display: none !important;
}

.vpd-date {
  font-size: 18px !important;
}
</style>
