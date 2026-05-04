
import { BrowserRouter, Route, Routes } from "react-router-dom";
import "./App.css";

import { useState } from "react";
import Login from "./pages/Login/login";
import Home from "./pages/Home/home";
import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer } from "react-toastify";


function App() {
  const [telaAtiva, setTelaAtiva] = useState("dashboard");
  const [show, setShow] = useState(true);

  return (
    <>
      <ToastContainer style={{zIndex: 1000000}} position="top-right" autoClose={3000} />
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Home show={show} setShow={setShow} telaAtiva={telaAtiva} setTelaAtiva={setTelaAtiva}/>} />
          <Route path="/login" element={<Login /> } />
        </Routes>
      </BrowserRouter>
    </>
  );
}

export default App;