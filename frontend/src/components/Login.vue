<template>
  <div class="loginPage login-background valign-wrapper">
    <!-- <div class="container"></div> -->

    <div class="row" v-if="showLogin">
      <div class="col s12 14 offset-14">
        <div class="card">
          <div class="card-action blue-grey darken-3 white-text">
            <h4>{{ $t(`text.otims_title`) }}</h4>
            <p style="color: #e2e2e2">{{ $t(`text.enter_credentials`) }}</p>

            <button
              @click="toggleLogin"
              class="waves-effect light-blue darken-4 btn-small"
            >
              User Manual
            </button>
          </div>

          <div class="card-content">
            <form id="loginForm" name="loginForm" @submit="loginNow">
              <div class="input-field">
                <i class="material-icons prefix">alternate_email</i>
                <label for="username">{{ $t(`text.email`) }}</label>
                <input
                  type="email"
                  id="username"
                  :placeholder="$t(`text.email`)"
                  v-model="login.email"
                  class="validate"
                  required
                  aria-required="true"
                />
              </div>
              <br />

              <div class="input-field">
                <i @click="togglePassword" class="material-icons prefix"
                  >lock</i
                >
                <label for="password">{{ $t(`text.password`) }}</label>
                <input
                  v-if="!showPassword"
                  type="password"
                  id="password"
                  :placeholder="$t(`text.password`)"
                  v-model="login.password"
                  class="validate"
                  required
                  aria-required="true"
                />

                <input
                  v-if="showPassword"
                  type="text"
                  id="passwordShow"
                  :placeholder="$t(`text.password`)"
                  v-model="login.password"
                  class="validate"
                  required
                  aria-required="true"
                />
              </div>

              <div class="form-field" id="langSection">
                <select v-model="$i18n.locale" @change="changeLocale(true)">
                  <option value="en">English</option>
                  <option value="ps">پښتو</option>
                  <option value="da">دری</option>
                </select>
              </div>

              <div class="form-field">
                <div class="switch">
                  <label>
                    {{ $t(`text.remember_me`) }}
                    <input
                      type="checkbox"
                      id="remember"
                      v-model="login.remember"
                    />
                    <span class="lever"></span>
                  </label>
                </div>
              </div>
              <br />

              <div class="form-field center-align">
                <button
                  class="btn-large light-blue darken-4 waves-effect"
                  form="loginForm"
                  type="submit"
                >
                  {{ $t(`text.login`) }}
                </button>
              </div>
            </form>
            <br />
          </div>
        </div>
      </div>
    </div>

    <div class="row" v-if="!showLogin">
      <div class="col s12 14 offset-14">
        <div class="card">
          <div class="card-action blue-grey darken-3 white-text">
            <h4>{{ $t(`text.otims_title`) }}</h4>
            <p style="color: #e2e2e2">
              You can download the user manuals in English and local languages
              from here.
            </p>

            <button
              @click="toggleLogin"
              class="waves-effect light-blue darken-4 btn-small"
            >
              Login
            </button>
          </div>

          <div class="card-content">
            <a
              href="otims_manual_english.pdf"
              class="waves-effect light-blue darken-4 btn-small"
              >Download User Manual (English)</a
            >
            <br />
            <br />
            <a
              href="otims_manual_pashto.pdf"
              class="waves-effect light-blue darken-4 btn-small"
              >Download User Manual (Pashto)</a
            >
            <br />
            <br />
            <a
              href="otims_manual_dari.pdf"
              class="waves-effect light-blue darken-4 btn-small"
              >Download User Manual (Dari)</a
            >

            <br />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Login",

  updated: function () {
    document.querySelector(".select-dropdown").style.color = "#fff";
  },

  mounted: function () {
    this.$forceUpdate();

    this.retrieveLoginCredentials();

    if (
      this.login.remember &&
      this.login.email.length > 1 &&
      this.login.password.length > 1
    ) {
      this.loginNow();
    }
  },
  computed: {},

  data() {
    return {
      showLogin: true,
      showPassword: false,

      login: {
        email: "",
        password: "",
        remember: true,
      },
    };
  },

  methods: {
    toggleLogin: function () {
      this.showLogin = !this.showLogin;
    },

    togglePassword: function () {
      this.showPassword = !this.showPassword;
    },

    changeLocale: function (isReload) {
      localStorage.locale = this.$i18n.locale;
      this.setLocale();
    },

    setLocale: function () {
      var body = document.querySelector("body");

      if (this.$i18n.locale == "en") {
        body.classList.remove("rtl");
        document.querySelector("html").dir = "ltr";
      } else {
        body.classList.add("rtl");
        document.querySelector("html").dir = "rtl";

        document.querySelector("body").style.fontFamily = "Tahoma";
      }
    },

    retrieveLoginCredentials() {
      if (localStorage.email) {
        this.login.email = localStorage.email;
      }

      if (localStorage.password) {
        this.login.password = localStorage.password;
      }

      if (localStorage.remember) {
        this.login.remember = localStorage.remember;
      }
    },

    persistLoginCredentials() {
      if (this.login.remember) {
        localStorage.email = this.login.email;
        localStorage.password = this.login.password;
        localStorage.remember = this.login.remember;
      }
    },

    loginNow: function (e) {
      if (e) {
        e.preventDefault();
      }

      console.log(this.login);

      var instance = this;

      this.$http
        .post("/login", {
          email: this.login.email.trim(),
          password: this.login.password,
        })
        .then(function (response) {
          instance.$store.commit("setUser", response.data.user);

          instance.setHeaderToken();
          instance.persistLoginCredentials();

          instance.$router.push("/home");
        })
        .catch(function (error) {
          console.log(error);
          console.log(error.response);
          alert(this.$t(`text.wrong_credentials`));
        });
    },

    setHeaderToken: function () {
      if (this.$store.getters.userObj) {
        console.log("api token: " + this.$store.getters.userObj.api_token);

        console.log(this.$http.defaults);

        // this.$http.defaults.headers.common["Authorization"] = "bearer " + this.$store.getters.userObj.api_token;

        this.$http.defaults.params = {
          api_token: this.$store.getters.userObj.api_token,
        };
      }
    },
  },
};
</script>

<style scoped>
.loginPage {
  background-repeat: no-repeat;
  background-position: left top;
  background-size: cover;
  background-color: #b0d7e6;
  height: 100vh;
  color: #fff;
}

.card {
  background: rgba(0, 0, 0, 0.5);
}

.loginPage label {
  font-size: 16px;
  color: #ccc;
}

.loginPage input {
  font-size: 14px !important;
  color: #fff;
}

.loginPage button:hover {
  padding: 0px 40px;
}
</style>
