import { useState } from "react";
import {
  Offcanvas,
  Nav,
  Button,
  Container,
  Stack,
  Card,
} from "react-bootstrap";
import { NavLink } from "react-router-dom";
import "../../App.css";
import style from "./sidebar.module.css";
import { MdDashboard } from "react-icons/md";
import { FaUser } from "react-icons/fa";
import { FaUsers } from "react-icons/fa";
import { IoIosCheckmarkCircle } from "react-icons/io";
import { MdTableBar } from "react-icons/md";

function SideBar({ telaAtiva, setTelaAtiva, show, setShow }) {

    console.log(telaAtiva)
    return (
        <>
            <Offcanvas
                className={style.sidebar}
                scroll={true}
                backdrop={false}
                show={show}
            >
                
                <Offcanvas.Body className="d-flex flex-column mt-5">
                    <div className="mb-auto">
                        <p className={style["font-casamento"]}>ADMIN</p>

                        <Stack gap={2} style={{ maxWidth: 450 }}>
                            <Button
                                variant="none"
                                onClick={() => setTelaAtiva('dashboard')}
                                className={`btn ${telaAtiva === "dashboard" ? style["botaoAtivo"] : ""}`}
                            >
                                <MdDashboard size={20} /> Dashboard
                            </Button>

                            
                        </Stack>

                        <p className={style["font-casamento"]}>ADMIN E CEREMONIALISTAS</p>

                        <Stack gap={2} style={{ maxWidth: 450 }}>
                            <Button
                                onClick={() => setTelaAtiva("convidados")}
                                className={`btn ${telaAtiva === "convidados" ? style.botaoAtivo : ""}`}
                            >
                                <FaUsers size={20} /> Convidados
                            </Button>

                            <Button
                                onClick={() => setTelaAtiva("checkins")}
                                className={`btn ${telaAtiva === "checkins" ? style.botaoAtivo : ""}`}
                            >
                                <IoIosCheckmarkCircle size={20} /> Checkins
                            </Button>
                            <Button
                                onClick={() => setTelaAtiva("mesas")}
                                className={`btn ${telaAtiva === "mesas" ? style.botaoAtivo : ""}`}
                            >
                                <MdTableBar size={20} /> Mesas
                            </Button>
                            <Button
                                onClick={() => setTelaAtiva("acompanhantes")}
                                className={`btn ${telaAtiva === "acompanhantes" ? style.botaoAtivo : ""}`}
                            >
                                <MdTableBar size={20} /> Acompanhantes
                            </Button>
                        </Stack>
                    </div>
                </Offcanvas.Body>
            </Offcanvas>
        </>
    )
}

export default SideBar