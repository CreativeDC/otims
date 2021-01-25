<template>
  <div>
    <div class="container" id="adminManageNodesPage" style="width: 100%;">
      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Node Levels</span>
              <p>This section displays the list of available node levels. Editing the node levels is not currently allowed.</p>

              <table class="striped" style="margin-top: 10px;">
                <thead>
                  <th>ID</th>
                  <th>Code</th>
                  <th>Title</th>
                  <th>Description</th>
                  <!-- <th>Action</th> -->
                </thead>
                <tbody>
                  <tr v-for="(level, index) in allLevels" v-bind:key="index">
                    <td>{{level.id}}</td>
                    <td>{{level.code}}</td>
                    <td>{{level.title}}</td>
                    <td>{{level.description}}</td>
                    <!-- <td>
                      <button class="waves-effect light-blue darken-4 btn btn-small">Edit</button>
                    </td>-->
                  </tr>
                </tbody>
              </table>
              <br />
              <!-- <button class="waves-effect light-blue darken-4 btn btn-small">Add New</button> -->
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Manage Nodes</span>
              <p>You can use this section to manage nodes on any level. You can also use this section to assign nodes to users.</p>
              <br />
              <div class="form-field">
                <!-- <label for="nodeShipmentRecipientLevel">Nodes Level</label> -->
                <select
                  id="nodeShipmentRecipientLevel"
                  name="nodeShipmentRecipientLevel"
                  v-model="nodes.level"
                >
                  <option value disabled selected>Select Level</option>
                  <option
                    v-for="(level, index) in allLevels"
                    v-bind:key="index"
                    :value="level.id"
                  >{{level.title}}</option>
                </select>

                <select
                  id="nodeShipmentRecipientLevel2"
                  name="nodeShipmentRecipientLevel2"
                  v-model="nodes.province"
                >
                  <option value disabled selected>Select Province</option>
                  <option
                    v-for="(province, index) in allProvinces"
                    v-bind:key="index"
                    :value="province.id"
                  >{{province.en_name}}</option>
                </select>

                <select
                  id="nodeShipmentRecipientLevel3"
                  name="nodeShipmentRecipientLevel3"
                  v-model="nodes.district"
                >
                  <option value disabled selected>Select District</option>
                  <option
                    v-for="(district, index) in nodeSelectedProvinceDistricts"
                    v-bind:key="index"
                    :value="district.id"
                  >{{district.en_name}}</option>
                </select>
              </div>
              <br />

              <table class="striped" style="margin-top: 10px;">
                <thead>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Parent ID</th>
                  <th>Level ID</th>
                  <th>Province ID</th>
                  <th>District ID</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  <tr v-for="(node, index) in nodeSelectedLevelChildNodes" v-bind:key="index">
                    <td>{{node.id}}</td>
                    <td>{{node.title}}</td>
                    <td>{{node.description}}</td>
                    <td>{{node.parent_id}}</td>
                    <td>{{node.level_id}}</td>
                    <td>{{node.province}}</td>
                    <td>{{node.district}}</td>
                    <td>
                      <button class="waves-effect light-blue darken-4 btn btn-small">Edit</button>
                      <button
                        @click="loadNodeCurrentUsers(node.id)"
                        href="#modalAssignUser"
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                      >Assign to User</button>
                    </td>
                  </tr>

                  <tr v-show="nodeSelectedLevelChildNodes.length < 1">
                    <td>No data available.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
              <br />
              <button
                v-if="nodes.level == 15 && nodes.province != 0 && nodes.district != 0"
                class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                href="#modalAddNode"
              >Add New</button>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Manage Users</span>
              <p>You can use this section to manage users as well as see what nodes are currently assigned to users.</p>
              <br />

              <div class="input-field">
                <label for="searchTerm">Name / Email</label>
                <input
                  id="searchTerm"
                  type="text"
                  name="searchTerm"
                  placeholder="Name / Email"
                  class="validate"
                  required
                  aria-required="true"
                  v-model="usersSection.searchTerm"
                  v-on:keyup.enter="usersSectionSearchUsers"
                />
              </div>

              <button
                @click="usersSectionSearchUsers"
                class="btn waves-effect light-blue darken-4"
              >Search</button>

              <br />

              <table class="striped" style="margin-top: 10px;">
                <thead>
                  <th>Full Name</th>
                  <th>Email</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  <tr v-for="user in usersSection.searchUsers" v-bind:key="user.id">
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>
                      <button
                        @click="displayChangeUserPasswordDialog(user.id)"
                        href="#modalChangeUserPassword"
                        class="btn-small waves-effect light-blue darken-4 modal-trigger"
                      >Change Password</button>
                      <button
                        @click="loadUserNodes(user.id)"
                        href="#modalViewUserNodes"
                        class="btn-small waves-effect light-blue darken-4 modal-trigger"
                      >View Nodes</button>
                    </td>
                  </tr>

                  <tr v-show="!usersSection.searchUsers.length > 0">
                    <td>No data available.</td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Assign Schools to District Users</span>
              <p>
                Since district level users are allowed to manage packages for schools under their respective districts,
                you can use this section to assign schools to district level users for which they will be able to manage packages.
              </p>
              <br />
              <div class="form-field">
                <select
                  id="nodeShipmentRecipientLevel"
                  name="nodeShipmentRecipientLevel"
                  v-model="assignNodes.level"
                >
                  <option value disabled selected>School Level</option>
                </select>

                <select v-model="assignNodes.province">
                  <option value disabled selected>Select Province</option>
                  <option
                    v-for="(province, index) in allProvinces"
                    v-bind:key="index"
                    :value="province.id"
                  >{{province.en_name}}</option>
                </select>

                <select v-model="assignNodes.district">
                  <option value disabled selected>Select District</option>
                  <option
                    v-for="(district, index) in assignNodesSelectedProvinceDistricts"
                    v-bind:key="index"
                    :value="district.id"
                  >{{district.en_name}}</option>
                </select>
              </div>
              <br />

              <div class="form-field">
                <select v-model="assignNodes.user">
                  <option value disabled selected>Select User</option>
                  <option
                    v-for="user in assignNodes.users"
                    v-bind:key="user.user_id"
                    :value="user.user_id"
                  >{{user.user_name}} ({{user.user_email}})</option>
                </select>
              </div>

              <div class="row">
                <div class="form-field col s5">
                  <label>Available Nodes ({{assignNodes.unassignedSchools.length}})</label>
                  <select
                    class="browser-default"
                    multiple
                    style="min-height: 400px;"
                    v-model="assignNodes.selectedAvailableNodes"
                  >
                    <option
                      v-for="school in assignNodes.unassignedSchools"
                      v-bind:key="school.id"
                      :value="school.id"
                    >{{school.title}}</option>
                  </select>
                </div>

                <div class="form-field col s2">
                  <div class="center-align" style="margin-top: 22px;">
                    <input
                      type="button"
                      value=">"
                      @click="assignSelectedSchools"
                      class="waves-effect waves-light btn-small"
                      style="width: 50px;"
                    />

                    <br />
                    <br />

                    <input
                      type="button"
                      value=">>"
                      @click="assignAllSchools"
                      class="waves-effect waves-light btn-small"
                      style="width: 50px;"
                    />
                    <br />
                    <br />

                    <input
                      type="button"
                      value="<"
                      @click="unassignSelectedSchools"
                      class="waves-effect waves-light btn-small"
                      style="width: 50px;"
                    />
                    <br />
                    <br />

                    <input
                      type="button"
                      value="<<"
                      @click="unassignAllSchools"
                      class="waves-effect waves-light btn-small"
                      style="width: 50px;"
                    />
                  </div>
                </div>

                <div class="form-field col s5">
                  <label>Assigned Nodes ({{assignNodes.assignedSchools.length}})</label>
                  <select
                    class="browser-default"
                    multiple
                    style="min-height: 400px;"
                    v-model="assignNodes.selectedAssignedNodes"
                  >
                    <option
                      v-for="school in assignNodes.assignedSchools"
                      v-bind:key="school.node_id"
                      :value="school.node_id"
                    >{{school.node_title}}</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- modalAddNode Modal Structure -->
    <div id="modalAddNode" class="modal">
      <div class="modal-content">
        <h4>Add New Node</h4>

        <div class="row">
          <div class="form-field">
            <label for="nodeTitle">Node Title</label>
            <input
              type="text"
              id="nodeTitle"
              name="nodeTitle"
              placeholder="Node Title"
              class="validate"
              required
              aria-required="true"
              v-model="nodes.title"
            />
          </div>

          <div class="form-field">
            <label for="nodeDescription">Description</label>
            <input
              type="text"
              id="nodeDescription"
              name="nodeDescription"
              placeholder="Node Title"
              class="validate"
              required
              aria-required="true"
              v-model="nodes.description"
            />
          </div>

          <div class="form-field">
            <label for="parentNode">Parent Node</label>
            <select id="parentNode" name="parentNode" v-model="nodes.parent">
              <option value disabled selected>Select Parent Node</option>
              <option
                v-for="(node, index) in nodeParentNode"
                v-bind:key="index"
                :value="node.id"
              >{{node.title}}</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button class="modal-close waves-effect btn-small" @click="addNode">Add Node</button>
          <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
        </div>
      </div>
    </div>

    <!-- modalAssignUser Modal Structure -->
    <div id="modalAssignUser" class="modal">
      <div class="modal-content">
        <h4>Assign Node to User</h4>
        <p>A user can manage anything related to an assigned node using their account. You can assign this node to a user by searching for and selecting a user below. The list of users this node is currently assigned to is also displayed below.</p>

        <br />

        <div class="row">
          <div class="form-field">
            <label for="searchTerm">Name / Email</label>
            <input
              id="searchTerm"
              type="text"
              name="searchTerm"
              placeholder="Name / Email"
              class="validate"
              required
              aria-required="true"
              v-model="assignSingleNode.searchTerm"
            />
          </div>

          <button @click="searchUsers" class="btn waves-effect light-blue darken-4">Search</button>
        </div>

        <table class="striped" style="margin-top: 10px;">
          <tbody>
            <tr v-for="user in assignSingleNode.searchUsers" v-bind:key="user.id">
              <td>{{user.name}}</td>
              <td>{{user.email}}</td>
              <td>
                <button
                  class="modal-close btn-small light-blue darken-4"
                  @click="assignNodeToUser(user.id)"
                >Assign</button>
              </td>
            </tr>
          </tbody>
        </table>

        <br />

        <table class="striped" style="margin-top: 10px;">
          <thead>
            <th>Full Name</th>
            <th>Email</th>
            <th>Action</th>
          </thead>
          <tbody>
            <tr v-for="user in assignSingleNode.currentUsers" v-bind:key="user.user_id">
              <td>{{user.user_name}}</td>
              <td>{{user.user_email}}</td>
              <td>
                <button
                  class="modal-close btn-small light-blue darken-4"
                  @click="unassignNodeFromUser(user.node_id, user.user_id)"
                >Remove</button>
              </td>
            </tr>

            <tr v-show="!assignSingleNode.currentUsers.length > 0">
              <td>No data available.</td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <div class="modal-footer">
          <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
        </div>
      </div>
    </div>

    <!-- modalViewUserNodes Modal Structure -->
    <div id="modalViewUserNodes" class="modal">
      <div class="modal-content">
        <h4>User Nodes</h4>
        <p>Below is the list of nodes currently assigned to the selected user. To assign new nodes, please use the Manage Nodes section.</p>

        <table class style="margin-top: 10px;">
          <tbody>
            <tr>
              <td style="font-weight: bold;">User ID</td>
              <td>{{usersSection.selectedUser}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">User Roles</td>
              <td>
                <li
                  v-for="role in usersSection.selectedUserRoles"
                  v-bind:key="role.id"
                >{{role.display_name}}</li>

                <span v-if="!usersSection.selectedUserRoles.length > 0">No role assigned yet.</span>
              </td>
            </tr>
          </tbody>
        </table>

        <table class="striped" style="margin-top: 10px;">
          <thead>
            <th>Node Title</th>
            <th>Node Level</th>
            <th>Action</th>
          </thead>
          <tbody>
            <tr v-for="node in usersSection.currentMainNodes" v-bind:key="node.node_id">
              <td>{{node.node_title}}</td>
              <td>{{node.level_title}}</td>
              <td>
                <button
                  @click="unassignNodeFromUser(node.node_id, node.user_id)"
                  class="modal-close btn-small light-blue darken-4"
                >Remove</button>
              </td>
            </tr>

            <tr v-show="!usersSection.currentMainNodes.length > 0">
              <td>No data available.</td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
        <hr />
        <table class="striped" style="margin-top: 10px;">
          <thead>
            <th>Node Title</th>
            <th>Node Level</th>
          </thead>
          <tbody>
            <tr v-for="node in usersSection.currentSchoolNodes" v-bind:key="node.node_id">
              <td>{{node.node_title}}</td>
              <td>{{node.level_title}}</td>
            </tr>

            <tr v-show="!usersSection.currentSchoolNodes.length > 0">
              <td>No data available.</td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <div class="modal-footer">
          <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
        </div>
      </div>
    </div>

    <!-- modalChangeUserPassword Modal Structure -->
    <div id="modalChangeUserPassword" class="modal">
      <div class="modal-content">
        <h4>Change User Password</h4>

        <div class="row">
          <div class="form-field">
            <label for="newPassword">New Password</label>
            <input
              type="password"
              id="newPassword"
              name="newPassword"
              placeholder="New Password"
              class="validate"
              required
              aria-required="true"
              v-model="usersSection.newPassword"
            />
          </div>
        </div>

        <div class="modal-footer">
          <button
            class="modal-close waves-effect btn-small"
            @click="changeUserPassword"
          >Change Password</button>
          <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdminManageNodes",

  mounted: function() {
    // console.log(this.$store.getters.userObj);
    // console.log(this.$store.getters.userNodeObj);
  },

  created() {
    this.getLevels();
    this.getProvinces();
  },

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  watch: {
    // school assignmnet section
    "assignNodes.province": function() {
      this.assignNodes.district = 0;

      this.getAssignNodesDistricts();
    },

    "assignNodes.district": function() {
      this.getAssignNodesAvailableSchools();
      this.getAssignNodesDistrictUsers();

      this.assignNodes.assignedSchools = [];
      this.assignNodes.user = 0;
      this.assignNodes.selectedAvailableNodes = [];
      this.assignNodes.selectedAssignedNodes = [];
    },

    "assignNodes.user": function() {
      this.getAssignNodesUserSchools();
    },
    // school assignment section

    // nodes management section
    // execute on level change
    "nodes.level": function() {
      this.nodes.province = 0;

      console.log(this.nodes.level);
    },

    // get districts of the selected province based on province change
    "nodes.province": function(selectedProvince, previousProvince) {
      this.nodes.district = 0;
      this.getNodeDistricts();

      this.nodeSelectedLevelChildNodes = [];
    },

    // get child nodes of selected level / province / district
    "nodes.district": function(selectedDistrict, previousDistrict) {
      this.getNodeLevelChildNodes();
      this.getNodeParentNode();
    }
    // nodes management section
  },

  data() {
    return {
      usersSection: {
        searchUsers: [],
        searchTerm: "",
        currentMainNodes: [],
        currentSchoolNodes: [],
        selectedUser: 0,
        selectedUserRoles: [],
        newPassword: ""
      },

      assignSingleNode: {
        selectedNode: 0,
        currentUsers: [],
        searchTerm: "",
        searchUsers: []
      },

      assignNodes: {
        level: 0,
        province: 0,
        district: 0,
        user: 0,
        availableSchools: [],
        users: [],
        assignedSchools: [],
        unassignedSchools: [],

        selectedAvailableNodes: [],
        selectedAssignedNodes: []
      },

      nodes: {
        level: 0,
        province: 0,
        district: 0,
        parent: 0,
        title: "",
        description: ""
      },

      allLevels: [],
      allProvinces: [],

      assignNodesSelectedProvinceDistricts: [],

      nodeSelectedProvinceDistricts: [],
      nodeSelectedLevelChildNodes: [],
      nodeParentNode: []
    };
  },

  methods: {
    displayChangeUserPasswordDialog: function(userId) {
      this.usersSection.selectedUser = userId;
      this.usersSection.newPassword = "";
    },
    changeUserPassword: function() {
      if (
        this.usersSection.newPassword.length >= 6 &&
        this.usersSection.selectedUser != 0
      ) {
        var passwordObj = {
          userId: this.usersSection.selectedUser,
          password: this.usersSection.newPassword
        };

        this.$http
          .post("/admin/users/password", passwordObj, {
            params: this.$http.defaults.params
          })
          .then(function(response) {
            console.log(response.data);
            alert(response.data.message);
          })
          .catch(function(error) {
            console.log(error.response);
            alert(error.response.data.message);
          });
      } else {
        alert("Please enter a valid password matching the password criteria.");
      }
    },

    loadUserNodes: function(userId) {
      this.usersSection.selectedUser = userId;

      this.loadUserRoles();
      this.loadUserMainNodes();
      this.loadUserSchoolNodes();
    },

    loadUserRoles: function() {
      var instance = this;

      this.$http
        .get("/admin/users/" + instance.usersSection.selectedUser + "/roles", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.usersSection.selectedUserRoles = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    loadUserMainNodes: function() {
      var instance = this;

      this.$http
        .get("/admin/users/" + instance.usersSection.selectedUser + "/nodes", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.usersSection.currentMainNodes = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    loadUserSchoolNodes: function() {
      var instance = this;

      this.$http
        .get(
          "/admin/users/" +
            instance.usersSection.selectedUser +
            "/nodes/schools",
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          console.log(response.data);

          instance.usersSection.currentSchoolNodes = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    usersSectionSearchUsers: function() {
      var instance = this;

      this.$http
        .get("/admin/users/search/" + instance.usersSection.searchTerm, {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);
          instance.usersSection.searchUsers = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    searchUsers: function() {
      var instance = this;

      this.$http
        .get("/admin/users/search/" + instance.assignSingleNode.searchTerm, {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);
          instance.assignSingleNode.searchUsers = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    assignNodeToUser(userId) {
      console.log(userId);
      console.log(this.assignSingleNode.selectedNode);

      var instance = this;

      this.$http
        .get(
          "/admin/nodes/assign/" +
            instance.assignSingleNode.selectedNode +
            "/" +
            userId,
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
        })
        .catch(function(error) {
          console.log(error);
          alert(error.response.data.message);
        });
    },

    unassignNodeFromUser(nodeId, userId) {
      var instance = this;

      this.$http
        .get("/admin/nodes/unassign/" + nodeId + "/" + userId, {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
        })
        .catch(function(error) {
          console.log(error);
          alert(error.response.data.message);
        });
    },

    loadNodeCurrentUsers(nodeId) {
      var instance = this;

      this.assignSingleNode.selectedNode = nodeId;
      this.assignSingleNode.searchUsers = [];

      this.$http
        .get("/admin/nodes/" + nodeId + "/users", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.assignSingleNode.currentUsers = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    addNode: function() {
      var instance = this;

      var nodeObj = {
        province: this.nodes.province,
        district: this.nodes.district,
        parent_id: this.nodes.parent,
        level_id: this.nodes.level,
        description: this.nodes.description,
        title: this.nodes.title,
        code: "0"
      };

      if (nodeObj.parent_id != 0 && nodeObj.title.length > 1) {
        this.$http
          .post("/admin/nodes/create", nodeObj, {
            params: this.$http.defaults.params
          })
          .then(function(response) {
            instance.getNodeLevelChildNodes();
            alert(response.data.message);
          })
          .catch(function(error) {
            console.log(error.response);
            alert("The node could not be created. Please try again.");
          });
      } else {
        alert(
          "Please select a parent node for the new node and then try again."
        );
      }
    },

    getLevels: function() {
      var instance = this;

      this.$http
        .get("/meta/levels", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.allLevels = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getNodeParentNode: function() {
      var instance = this;

      this.nodeParentNode = [];

      if (
        this.nodes.level != 0 &&
        this.nodes.province != 0 &&
        this.nodes.district != 0
      ) {
        this.$http
          .get(
            "/meta/levels/" +
              14 +
              "/" +
              this.nodes.province +
              "/" +
              this.nodes.district +
              "/nodes",
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            console.log(response.data);
            instance.nodeParentNode = response.data;
          })
          .catch(function(error) {
            console.log(error);
          });
      }
    },

    getNodeLevelChildNodes: function() {
      var instance = this;

      this.nodeSelectedLevelChildNodes = [];

      if (
        this.nodes.level != 0 &&
        this.nodes.province != 0 &&
        this.nodes.district != 0
      ) {
        this.$http
          .get(
            "/meta/levels/" +
              this.nodes.level +
              "/" +
              this.nodes.province +
              "/" +
              this.nodes.district +
              "/nodes",
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            console.log(response.data);
            instance.nodeSelectedLevelChildNodes = response.data;
          })
          .catch(function(error) {
            console.log(error);
          });
      }
    },

    getProvinces: function() {
      var instance = this;

      this.$http
        .get("/meta/provinces", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          // console.log(response.data);

          instance.allProvinces = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getNodeDistricts: function() {
      var instance = this;

      this.$http
        .get("/meta/provinces/" + this.nodes.province + "/districts", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.nodeSelectedProvinceDistricts = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getAssignNodesDistricts: function() {
      var instance = this;

      this.$http
        .get("/meta/provinces/" + this.assignNodes.province + "/districts", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.assignNodesSelectedProvinceDistricts = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getAssignNodesAvailableSchools: function() {
      var instance = this;

      this.$http
        .get(
          "/admin/nodes/" +
            this.assignNodes.province +
            "/" +
            this.assignNodes.district +
            "/schools",
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          instance.assignNodes.availableSchools = response.data;
          instance.assignNodes.unassignedSchools = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getAssignNodesDistrictUsers: function() {
      var instance = this;

      this.$http
        .get(
          "/admin/nodes/" +
            this.assignNodes.province +
            "/" +
            this.assignNodes.district +
            "/users",
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          instance.assignNodes.users = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getAssignNodesUserSchools: function() {
      var instance = this;

      this.$http
        .get("/admin/nodes/" + this.assignNodes.user + "/schools", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.assignNodes.assignedSchools = response.data;
          instance.filterAssignNodesAvailableSchools();
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    filterAssignNodesAvailableSchools: function() {
      var availableArray = this.assignNodes.availableSchools;
      var assignedArray = this.assignNodes.assignedSchools;

      var unAssignedArray = availableArray.filter(function(availableNode) {
        var result = assignedArray.find(function(assignedNode) {
          return assignedNode.node_id == availableNode.id;
        });

        if (result == undefined) {
          return true;
        }
      });

      this.assignNodes.unassignedSchools = unAssignedArray;
    },

    assignSelectedSchools: function() {
      if (
        this.assignNodes.province > 0 &&
        this.assignNodes.district > 0 &&
        this.assignNodes.user > 0
      ) {
        console.log(this.assignNodes.selectedAvailableNodes);

        var instance = this;

        this.$http
          .post(
            "/admin/nodes/user/schools/assign",
            {
              assignSchools: instance.assignNodes.selectedAvailableNodes,
              user_id: instance.assignNodes.user
            },
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            instance.getAssignNodesUserSchools();
            console.log(response.data);
            alert(response.data.message);
          })
          .catch(function(error) {
            console.log(error.response);
            alert(error.response.data.message);
          });
      }
    },

    assignAllSchools: function() {
      if (
        this.assignNodes.province > 0 &&
        this.assignNodes.district > 0 &&
        this.assignNodes.user > 0
      ) {
        var assignSchools = this.assignNodes.unassignedSchools.map(function(
          school
        ) {
          return school.id;
        });

        console.log(assignSchools);

        var instance = this;

        this.$http
          .post(
            "/admin/nodes/user/schools/assign",
            {
              assignSchools: assignSchools,
              user_id: instance.assignNodes.user
            },
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            instance.getAssignNodesUserSchools();

            console.log(response.data);
            alert(response.data.message);
          })
          .catch(function(error) {
            console.log(error.response);
            alert(error.response.data.message);
          });
      }
    },

    unassignSelectedSchools: function() {
      if (
        this.assignNodes.province > 0 &&
        this.assignNodes.district > 0 &&
        this.assignNodes.user > 0
      ) {
        console.log(this.assignNodes.selectedAssignedNodes);

        var instance = this;

        this.$http
          .post(
            "/admin/nodes/user/schools/unassign",
            {
              unassignSchools: instance.assignNodes.selectedAssignedNodes,
              user_id: instance.assignNodes.user
            },
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            instance.getAssignNodesUserSchools();

            console.log(response.data);
            alert(response.data.message);
          })
          .catch(function(error) {
            console.log(error.response);
            alert(error.response.data.message);
          });
      }
    },

    unassignAllSchools: function() {
      if (
        this.assignNodes.province > 0 &&
        this.assignNodes.district > 0 &&
        this.assignNodes.user > 0
      ) {
        var unassignSchools = this.assignNodes.assignedSchools.map(function(
          school
        ) {
          return school.node_id;
        });

        console.log(unassignSchools);

        var instance = this;

        this.$http
          .post(
            "/admin/nodes/user/schools/unassign",
            {
              unassignSchools: unassignSchools,
              user_id: instance.assignNodes.user
            },
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            instance.getAssignNodesUserSchools();

            console.log(response.data);
            alert(response.data.message);
          })
          .catch(function(error) {
            console.log(error.response);
            alert(error.response.data.message);
          });
      }
    }
  }
};
</script>


<style scoped>
.collapsible-body {
  background-color: #fff !important;
}

.receivedStatus {
  text-align: center;
  font-size: 12px;
  border-radius: 3px;
  color: #fff;
  background-color: #2bbbad;
  padding: 1px;
  width: 70px;
}

.pendingStatus {
  text-align: center;
  font-size: 12px;
  border-radius: 3px;
  color: #fff;
  background-color: #db400d;
  padding: 1px;
  width: 70px;
}
</style>