import { Container } from "react-bootstrap";
import { BrowserRouter, Route, Routes } from "react-router-dom";
import "./App.css";

import { useState } from "react";
import Home from "./pages/Home/home";
import Login from "./pages/Login/login";


function App() {
  const [telaAtiva, setTelaAtiva] = useState("dashboard");
  const [show, setShow] = useState(true);

  return (
    <>
      <BrowserRouter>
        <Routes>
          <Route
            path="/"
            element={
              <Home
                telaAtiva={telaAtiva}
                setTelaAtiva={setTelaAtiva}
                show={show}
                setShow={setShow}
              />
            }
          />
          <Route path="/login" element={<Login /> } />
        </Routes>
      </BrowserRouter>
    </>
  );
}

export default App;
