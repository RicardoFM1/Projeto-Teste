
import { useState } from "react";
import { Container, Nav, Button, Navbar } from "react-bootstrap";
import { RiMenuUnfoldFill } from "react-icons/ri";
import { RiMenuFoldFill } from "react-icons/ri";
import style from "./header.module.css"


function Header({ telaAtiva, setTelaAtiva, show, setShow }) {

    return (
        <>
            <Navbar className={style.header} bg="dark" variant="dark" expand="lg" as="header">
                <Container fluid>
                    <Button className="m-1 ignorar-fonte-btn" variant="link" onClick={() => setShow(!show)}>
                        {show ? <RiMenuFoldFill size={25} /> : <RiMenuUnfoldFill size={25} />}
                    </Button>
                <Navbar.Brand href="/">Senac Wedding</Navbar.Brand>

                </Container>
            </Navbar>
        </>
    );
}

export default Header;