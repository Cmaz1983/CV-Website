<div class="contact" id="contact">
        <div class="overlay">
            <div class="container pt-2">
                <div class="contact-inner pt-5">
                    <div class="contact-title text-center">
                        <h4>Contact me</h4>
                    </div>
                    <div class="contact-sub text-center">
                        Have any questions? I'd love to hear from you!
                    </div>
                    <div class="contact-form">
                        <form id="contact-form" onsubmit="contactHandler()">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label>Full name
                                    <input type="text" class="form-control" placeholder="*">
                                    <div class="invalid-feedback">
                                        Please enter name
                                    </div>
                                    </label>
                                    
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Subject
                                    <input type="text" class="form-control" placeholder="*">
                                    <div class="invalid-feedback">
                                        Please enter subject
                                    </div>
                                    </label>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Email Address
                                    <input type="text" class="form-control" placeholder="*">
                                    <div class="invalid-feedback">
                                        Please enter valid email address
                                    </div>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label>Message
                                    <textarea class="form-control" placeholder="type message here..."></textarea>
                                    <div class="invalid-feedback">
                                        Please enter message
                                    </div>
                                    </label>
                                </div>
                            </div>
                            <div class="float-left" id="contact-error">This is an error</div>
                            <div class="float-left" id="contact-message">This is a message</div>
                            <button type="submit" class="btn btn-primary float-right">Send message</button>
                        </div>
                    </form>
                </div>        
            </div>
                    <div style="clear:both;"></div>
        </div>

    </div>