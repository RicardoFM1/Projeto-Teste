import { Button, Container, Navbar } from "react-bootstrap";
import style from "./header.module.css"
import { AiOutlineMenuFold } from "react-icons/ai";
import { AiOutlineMenuUnfold } from "react-icons/ai";

function Header({ telaAtiva, setTelaAtiva, show, setShow }) {
  console.log(show)
  return (
  
  
    <Navbar expand='lg' className={style.header}>
      <Button onClick={() => setShow(!show)}>{show ? <AiOutlineMenuFold size={25} color="black" /> : <AiOutlineMenuUnfold size={25} color="black"/>}</Button>
      <Navbar.Brand className="mx-2">Senac Wedding</Navbar.Brand>
    </Navbar>
  

  );
}

export default Header;