import { Col, Container, Row } from "react-bootstrap";
import Header from "../../components/Header/header";
import SideBar from "../../components/Sidebar/sidebar";
import Dashboard from "../../components/Dashboard/dashboard";
import Usuarios from "../../components/Usuarios/usuarios";
import ConvidadosAcompanhantes from "../../components/ConvidadosAcompanhantes/convidadosAcompanhantes";
import Checkin from "../../components/Checkins/checkin";
import Mesas from "../../components/Mesas/mesas";
import { useEffect, useState } from "react";
import Api from "../../API/api";

function Home({ telaAtiva, setTelaAtiva, show, setShow }) {


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
            <main style={{ minHeight: "100vh", padding: "20px", transition: "all 0.5s" }}>
              {/* checar admin */}
              {telaAtiva === "dashboard" && <Dashboard />}
              {telaAtiva === "usuarios" && <Usuarios />}
              {telaAtiva === "convidados_acompanhantes" && <ConvidadosAcompanhantes />}
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
