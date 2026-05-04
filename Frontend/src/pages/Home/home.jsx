import { Col, Container, Row } from "react-bootstrap";
import Header from "../../components/Header/header";
import SideBar from "../../components/Sidebar/sidebar";
import Dashboard from "../../components/Dashboard/dashboard";
import Convidados from "../../components/Convidados/convidados";
import Checkin from "../../components/Checkins/checkin";
import Mesas from "../../components/Mesas/mesas";
import Api from "../../API/api";
import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";

function Home({ telaAtiva, setTelaAtiva, show, setShow }) {
  const navigate = useNavigate();
  
  useEffect(() => {
   const token = localStorage.getItem("token");
    if (!token) {
      navigate("/login");
    }
  }, []);


  return (
    <>
      <Header
        telaAtiva={telaAtiva}
        setTelaAtiva={setTelaAtiva}
        show={show}
        setShow={setShow}
      />
      <Container fluid className="px-0">
        <Row className="g-0">
          {show && (
            <Col lg={3} className="px-0 d-none d-lg-block">
              <SideBar
                telaAtiva={telaAtiva}
                setTelaAtiva={setTelaAtiva}
                show={show}
                setShow={setShow}
              />
            </Col>
          )}

          <Col xs={12} lg={show ? 8 : 12} className="px-5">
            <main
              style={{
                minHeight: "100vh",
                padding: "20px",
                transition: "all 0.5s",
              }}
            >
              {telaAtiva === "dashboard" && <Dashboard />}
              {telaAtiva === "convidados" && <Convidados />}
              {telaAtiva === "checkins" && <Checkin />}
              {telaAtiva === "mesas" && <Mesas />}
            </main>
          </Col>
        </Row>
      </Container>
    </>
  );
}

export default Home;
