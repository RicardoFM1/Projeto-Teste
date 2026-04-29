import axios from "axios";

const token = localStorage.getItem("token");

const Api = axios.create({
  baseURL: "http://localhost:3000",
  timeout: 10000,
  headers: {
    "Content-Type": "application/json",
    Authorization: "Bearer " + token
  },
});

export default Api
