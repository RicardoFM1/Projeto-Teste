import { useState } from "react";
import { Navbar, Container, Nav, Button } from "react-bootstrap";
import SideBar from "../Sidebar/sidebar";
import { RiMenuUnfoldFill } from "react-icons/ri";
import { RiMenuFoldFill } from "react-icons/ri";
import style from "./header.module.css"

function Header({telaAtiva, setTelaAtiva}) {
const [show, setShow] = useState(true)

  return (
    <>
    <Navbar className={style.header} bg="dark" variant="dark" expand="lg" as="header">
      <Container fluid>
         <Button className="m-1 ignorar-fonte-btn" variant="link" onClick={() => setShow(!show)}>
          {show ? <RiMenuFoldFill size={25}/> : <RiMenuUnfoldFill size={25}/> }
        </Button>
        <Navbar.Brand href="/">Senac Wedding</Navbar.Brand>
        <Navbar.Collapse id="header">
         
        </Navbar.Collapse>
      </Container>
    </Navbar>

    <SideBar telaAtiva={telaAtiva} setTelaAtiva={setTelaAtiva} show={show}/>
    </>
  );
}

export default Header;
