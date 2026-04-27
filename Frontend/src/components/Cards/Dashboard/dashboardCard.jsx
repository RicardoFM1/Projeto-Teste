import {Card} from 'react-bootstrap'
import style from "./dashboardCard.module.css"

function DashboardCard () {
    return (
        <Card  className={style.Card}>
         
            <Card.Title className={style.CardTitle}>Checkins</Card.Title>
           
           <Card.Body>
            <Card.Text style={{fontSize: '30px', display: 'flex', justifyContent: 'center'}}>
                100
            </Card.Text>
           </Card.Body>
        </Card>
    )
}


export default DashboardCard